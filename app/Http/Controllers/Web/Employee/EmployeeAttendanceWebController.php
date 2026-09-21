<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class EmployeeAttendanceWebController extends Controller
{
    /**
     * GET /employee/attendance
     *
     * Halaman "detail absensi" versi web untuk role employee (lingkup
     * company). Ini mengisi 3 variabel yang dipakai oleh
     * resources/views/pages/employee/attendance/index.blade.php:
     *   - $status  → status check-in/out HARI INI
     *   - $summary → rekap bulan berjalan (hadir/terlambat/izin/cuti/alpha)
     *   - $history → riwayat absensi (paginated) untuk bulan & tahun terpilih
     */
    public function index(Request $request)
    {
        $user      = $request->user();
        $companyId = $user->company_id;
        $today     = Carbon::today()->toDateString();

        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year', now()->year);

        // ── status hari ini ──────────────────────────────────────────
        $todayAttendance = Attendance::where('company_id', $companyId)
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $status = [
            'is_checked_in'  => (bool) ($todayAttendance?->time_in),
            'is_checked_out' => (bool) ($todayAttendance?->time_out),
            'check_in_time'  => $todayAttendance?->time_in,
            'check_out_time' => $todayAttendance?->time_out,
        ];

        // ── rekap bulan terpilih ─────────────────────────────────────
        $monthAttendances = Attendance::where('company_id', $companyId)
            ->where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $present  = $monthAttendances->whereNotNull('time_in')->count();
        $late     = $monthAttendances->where('status', 'late')->count();
        $absent   = $monthAttendances->where('status', 'absent')->count();
        $permit   = $monthAttendances->whereIn('status', ['permitted'])->count();
        $onLeave  = $monthAttendances->whereIn('status', ['on_leave'])->count();

        $workingDaysSoFar = max($present + $absent, 1);
        $attendanceRate   = round(($present / $workingDaysSoFar) * 100);

        $summary = [
            'present'          => $present,
            'late'             => $late,
            'permitted'        => $permit,
            'on_leave'         => $onLeave,
            'absent'           => $absent,
            'attendance_rate'  => $attendanceRate,
        ];

        // ── riwayat (paginated) ──────────────────────────────────────
        $paginator = Attendance::where('company_id', $companyId)
            ->where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->with('shift')
            ->orderByDesc('date')
            ->paginate(15)
            ->withQueryString();

        // ->resolve() per item supaya Blade bisa akses $row['check_in_time']
        // dkk sebagai array hasil transformasi, bukan ArrayAccess ke model asli.
        $history = [
            'data'         => collect($paginator->items())
                ->map(fn ($a) => (new AttendanceResource($a))->resolve())
                ->all(),
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
        ];

        return view('pages.employee.attendance.index', compact('status', 'summary', 'history', 'month', 'year'));
    }

    /**
     * GET /employee/attendance/{id}
     * Detail satu record absensi (dipakai untuk drill-down dari riwayat).
     */
    public function show(Request $request, $tenant, $id)
    {
        $user = $request->user();

        $attendance = Attendance::where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->with('shift')
            ->findOrFail($id);

        return view('pages.employee.attendance.show', [
            // ->resolve() supaya di Blade $attendance['check_in_time'] dst
            // mengambil dari array HASIL TRANSFORMASI resource, bukan
            // ArrayAccess ke kolom asli model (yang namanya beda, mis.
            // time_in vs check_in_time).
            'attendance' => (new AttendanceResource($attendance))->resolve(),
        ]);
    }

    /**
     * POST /employee/attendance/check-in
     */
    public function checkIn(Request $request): RedirectResponse
    {
        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $user    = $request->user();
        $company = $user->company;
        $today   = Carbon::today()->toDateString();
        $now     = Carbon::now();

        $existing = Attendance::where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing && $existing->time_in) {
            return back()->with('error', 'Anda sudah check-in hari ini pukul ' . $existing->time_in);
        }

        $distanceKm = $this->haversineKm(
            (float) $company->latitude,
            (float) $company->longitude,
            (float) $request->latitude,
            (float) $request->longitude
        );

        if ($distanceKm > (float) $company->radius_km) {
            return back()->with('error', sprintf(
                'Anda berada di luar radius lokasi (%s m dari batas %s m).',
                number_format($distanceKm * 1000, 0),
                number_format((float) $company->radius_km * 1000, 0)
            ));
        }

        $shift        = $this->resolveShift($user, $today);
        $scheduledIn  = $this->getScheduledTime($shift?->start_time ?? $company->time_in, $today);
        $lateMinutes  = $this->calcLateMinutes($scheduledIn, $shift?->grace_period_minutes ?? 0, $now);

        $attendance = Attendance::firstOrNew([
            'company_id' => $company->id,
            'user_id'    => $user->id,
            'date'       => $today,
        ]);

        $attendance->company_id   = $company->id;
        $attendance->user_id      = $user->id;
        $attendance->date         = $today;
        $attendance->shift_id     = $shift?->id;
        $attendance->scheduled_in = $scheduledIn?->format('H:i:s');
        $attendance->time_in      = $now->format('H:i:s');
        $attendance->latlon_in    = $request->latitude . ',' . $request->longitude;
        $attendance->status       = $lateMinutes > 0 ? 'late' : 'on_time';
        $attendance->late_minutes = $lateMinutes;
        $attendance->save();

        $msg = 'Check-in berhasil.';
        if ($lateMinutes > 0) {
            $msg .= " Anda terlambat {$lateMinutes} menit.";
        }

        return redirect()->route('employee.attendance.index')->with('success', $msg);
    }

    /**
     * POST /employee/attendance/check-out
     */
    public function checkOut(Request $request): RedirectResponse
    {
        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $user    = $request->user();
        $company = $user->company;
        $today   = Carbon::today()->toDateString();
        $now     = Carbon::now();

        $attendance = Attendance::where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->time_in) {
            return back()->with('error', 'Anda belum check-in hari ini.');
        }

        if ($attendance->time_out) {
            return back()->with('error', 'Anda sudah check-out hari ini pukul ' . $attendance->time_out);
        }

        $shift        = $attendance->shift_id ? Shift::find($attendance->shift_id) : null;
        $scheduledOut = $this->getScheduledTime($shift?->end_time ?? $company->time_out, $today);
        $earlyMinutes = $scheduledOut && $now->lt($scheduledOut)
            ? (int) $scheduledOut->diffInMinutes($now)
            : 0;

        $attendance->time_out            = $now->format('H:i:s');
        $attendance->latlon_out          = $request->latitude . ',' . $request->longitude;
        $attendance->early_leave_minutes = $earlyMinutes;
        $attendance->save();

        $msg = 'Check-out berhasil.';
        if ($earlyMinutes > 0) {
            $msg .= " Anda pulang {$earlyMinutes} menit lebih awal.";
        }

        return redirect()->route('employee.attendance.index')->with('success', $msg);
    }

    // ----------------------------------------------------------
    // HELPERS (versi ringkas dari App\Http\Controllers\Api\Employee\EmployeeAttendanceController,
    // dipakai supaya versi web mengikuti aturan jam/telat yang sama)
    // ----------------------------------------------------------

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function resolveShift($user, string $date): ?Shift
    {
        $override = DB::table('user_shift_overrides')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('start_date', '<=', $date)
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $date))
            ->whereNull('deleted_at')
            ->orderByDesc('start_date')
            ->first();

        if ($override) {
            return Shift::find($override->shift_id);
        }

        return Shift::where('company_id', $user->company_id)
            ->where('is_default', true)
            ->first();
    }

    private function getScheduledTime(?string $time, string $date): ?Carbon
    {
        return $time ? Carbon::parse($date . ' ' . $time) : null;
    }

    private function calcLateMinutes(?Carbon $scheduledIn, int $graceMinutes, Carbon $actualIn): int
    {
        if (!$scheduledIn) {
            return 0;
        }

        $deadline = $scheduledIn->copy()->addMinutes($graceMinutes);

        return $actualIn->lte($deadline) ? 0 : (int) $actualIn->diffInMinutes($scheduledIn);
    }
}
