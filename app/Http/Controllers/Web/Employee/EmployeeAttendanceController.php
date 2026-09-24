<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeAttendanceController extends Controller
{
    // ----------------------------------------------------------
    // HELPERS — port 1:1 dari Api\Employee\EmployeeAttendanceController
    // supaya perilaku check-in/out di web identik dengan mobile.
    // ----------------------------------------------------------

    private function ensureEmployee(): void
    {
        if (! Auth::check() || ! in_array(Auth::user()->role, ['employee', 'santri', 'student'], true)) {
            abort(403, 'Akses ditolak (khusus employee).');
        }
    }

    private function me(): User
    {
        return Auth::user();
    }

    private function companyOrFail()
    {
        $company = $this->me()->company;
        if (! $company) {
            abort(422, 'Company tidak ditemukan untuk user ini.');
        }
        return $company;
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function resolveShift(User $user, string $date): ?Shift
    {
        // Guard: fitur shift group / override bersifat opsional. Kalau
        // migration-nya belum dijalankan di environment ini, jangan sampai
        // dashboard/absensi ikut crash — langsung fallback ke default shift.
        if (! Schema::hasTable('user_shift_overrides') || ! Schema::hasTable('shift_group_users')) {
            return Shift::where('company_id', $user->company_id)
                ->where('is_default', true)
                ->first();
        }

        $override = DB::table('user_shift_overrides')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('start_date', '<=', $date)
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $date))
            ->whereNull('deleted_at')
            ->orderByDesc('start_date')
            ->first();

        if ($override) return Shift::find($override->shift_id);

        $groupIds = DB::table('shift_group_users')
            ->where('user_id', $user->id)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $date))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $date))
            ->pluck('shift_group_id');

        if ($groupIds->isNotEmpty() && Schema::hasTable('shift_group_assignments')) {
            $assignment = DB::table('shift_group_assignments')
                ->whereIn('shift_group_id', $groupIds)
                ->where('company_id', $user->company_id)
                ->where('start_date', '<=', $date)
                ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $date))
                ->whereNull('deleted_at')
                ->orderByDesc('start_date')
                ->first();

            if ($assignment) return Shift::find($assignment->shift_id);
        }

        return Shift::where('company_id', $user->company_id)
            ->where('is_default', true)
            ->first();
    }

    private function getScheduledIn(?Shift $shift, $company, string $date): ?Carbon
    {
        $time = $shift ? $shift->start_time : $company?->time_in;
        return $time ? Carbon::parse($date . ' ' . $time) : null;
    }

    private function getScheduledOut(?Shift $shift, $company, string $date): ?Carbon
    {
        $time = $shift ? $shift->end_time : $company?->time_out;
        return $time ? Carbon::parse($date . ' ' . $time) : null;
    }

    private function calcLateMinutes(?Shift $shift, $company, Carbon $actualIn, string $date): int
    {
        $scheduledIn = $this->getScheduledIn($shift, $company, $date);
        if (! $scheduledIn) return 0;
        $grace    = $shift?->grace_period_minutes ?? 0;
        $deadline = $scheduledIn->copy()->addMinutes($grace);
        if ($actualIn->lte($deadline)) return 0;
        return (int) $actualIn->diffInMinutes($scheduledIn);
    }

    private function calcEarlyLeaveMinutes(?Shift $shift, $company, Carbon $actualOut, string $date): int
    {
        $scheduledOut = $this->getScheduledOut($shift, $company, $date);
        if (! $scheduledOut) return 0;
        if ($actualOut->gte($scheduledOut)) return 0;
        return (int) $scheduledOut->diffInMinutes($actualOut);
    }

    // ----------------------------------------------------------
    // GET /employee/attendances
    // Halaman utama: status hari ini + tombol check-in/out + riwayat
    // ----------------------------------------------------------
    public function index(Request $request): View
    {
        $this->ensureEmployee();

        $user    = $this->me();
        $company = $this->companyOrFail();
        $today   = Carbon::today()->toDateString();

        $attendance = Attendance::where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $shift        = $this->resolveShift($user, $today);
        $scheduledIn  = $this->getScheduledIn($shift, $company, $today);
        $scheduledOut = $this->getScheduledOut($shift, $company, $today);

        $limit = (int) $request->query('limit', 15);
        $limit = max(1, min($limit, 100));

        $history = Attendance::where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->with('shift')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        return view('pages.employee.attendance.index', [
            'company'      => $company,
            'attendance'   => $attendance,
            'shift'        => $shift,
            'scheduledIn'  => $scheduledIn,
            'scheduledOut' => $scheduledOut,
            'history'      => $history,
            'today'        => $today,
            'faceMissing'  => empty($user->face_embedding),
        ]);
    }

    // ----------------------------------------------------------
    // POST /employee/attendances/check-in
    // Body: latitude, longitude (diisi otomatis via JS geolocation)
    // ----------------------------------------------------------
    public function checkIn(Request $request): RedirectResponse
    {
        $this->ensureEmployee();

        $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ], [
            'latitude.required'  => 'Lokasi tidak terdeteksi. Izinkan akses lokasi lalu coba lagi.',
            'longitude.required' => 'Lokasi tidak terdeteksi. Izinkan akses lokasi lalu coba lagi.',
        ]);

        $user    = $this->me();
        $company = $this->companyOrFail();
        $today   = Carbon::today()->toDateString();
        $now     = Carbon::now();

        if (empty($user->face_embedding)) {
            return back()->with('error', 'Anda belum melakukan registrasi wajah. Silakan registrasi wajah melalui aplikasi mobile terlebih dahulu.');
        }

        $existing = Attendance::where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing && $existing->time_in) {
            return back()->with('error', 'Sudah check-in hari ini pada pukul ' . $existing->time_in);
        }

        $distanceKm = $this->haversineKm(
            (float) $company->latitude,
            (float) $company->longitude,
            (float) $request->latitude,
            (float) $request->longitude
        );

        if ($distanceKm > (float) $company->radius_km) {
            return back()->with('error', sprintf(
                'Anda berada di luar radius perusahaan (%s m dari batas %s m)',
                number_format($distanceKm * 1000, 0),
                number_format((float) $company->radius_km * 1000, 0)
            ));
        }

        $shift        = $this->resolveShift($user, $today);
        $scheduledIn  = $this->getScheduledIn($shift, $company, $today);
        $scheduledOut = $this->getScheduledOut($shift, $company, $today);
        $lateMinutes  = $this->calcLateMinutes($shift, $company, $now, $today);

        $attendance = Attendance::firstOrNew([
            'company_id' => $company->id,
            'user_id'    => $user->id,
            'date'       => $today,
        ]);

        $attendance->company_id    = $company->id;
        $attendance->user_id       = $user->id;
        $attendance->date          = $today;
        $attendance->marked_by     = null;
        $attendance->shift_id      = $shift?->id;
        $attendance->scheduled_in  = $scheduledIn?->format('H:i:s');
        $attendance->scheduled_out = $scheduledOut?->format('H:i:s');
        $attendance->time_in       = $now->format('H:i:s');
        $attendance->latlon_in     = $request->latitude . ',' . $request->longitude;
        $attendance->status        = $lateMinutes > 0 ? 'late' : 'on_time';
        $attendance->late_minutes  = $lateMinutes;
        $attendance->face_verified = false;
        $attendance->save();

        $msg = 'Check-in berhasil.';
        if ($lateMinutes > 0) $msg .= " (Terlambat {$lateMinutes} menit)";

        return redirect(member_route('attendance.index'))->with('success', $msg);
    }

    // ----------------------------------------------------------
    // POST /employee/attendances/check-out
    // ----------------------------------------------------------
    public function checkOut(Request $request): RedirectResponse
    {
        $this->ensureEmployee();

        $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ], [
            'latitude.required'  => 'Lokasi tidak terdeteksi. Izinkan akses lokasi lalu coba lagi.',
            'longitude.required' => 'Lokasi tidak terdeteksi. Izinkan akses lokasi lalu coba lagi.',
        ]);

        $user    = $this->me();
        $company = $this->companyOrFail();
        $today   = Carbon::today()->toDateString();
        $now     = Carbon::now();

        $attendance = Attendance::where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (! $attendance || ! $attendance->time_in) {
            return back()->with('error', 'Belum check-in hari ini.');
        }

        if ($attendance->time_out) {
            return back()->with('error', 'Sudah check-out hari ini pada pukul ' . $attendance->time_out);
        }

        $shift             = $attendance->shift_id ? Shift::find($attendance->shift_id) : null;
        $earlyLeaveMinutes = $this->calcEarlyLeaveMinutes($shift, $company, $now, $today);

        $attendance->time_out            = $now->format('H:i:s');
        $attendance->latlon_out          = $request->latitude . ',' . $request->longitude;
        $attendance->early_leave_minutes = $earlyLeaveMinutes;
        $attendance->save();

        $msg = 'Check-out berhasil.';
        if ($earlyLeaveMinutes > 0) $msg .= " (Pulang {$earlyLeaveMinutes} menit lebih awal)";

        return redirect(member_route('attendance.index'))->with('success', $msg);
    }

    // ----------------------------------------------------------
    // GET /employee/attendances/{id}
    // ----------------------------------------------------------
    public function show(int $id): View
    {
        $this->ensureEmployee();

        $user    = $this->me();
        $company = $this->companyOrFail();

        $a = Attendance::with('shift')
            ->where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $attendance = [
            'date'                => $a->date,
            'status'              => $a->status,
            'shift_name'          => $a->shift->name ?? null,
            'check_in_time'       => $a->time_in,
            'check_out_time'      => $a->time_out,
            'late_minutes'        => (int) ($a->late_minutes ?? 0),
            'early_leave_minutes' => (int) ($a->early_leave_minutes ?? 0),
            'overtime_minutes'    => (int) ($a->overtime_minutes ?? 0),
            'latlon_in'           => $a->latlon_in,
            'latlon_out'          => $a->latlon_out,
        ];

        return view('pages.employee.attendance.show', [
            'attendance' => $attendance,
        ]);
    }
}