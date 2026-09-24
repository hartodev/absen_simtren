<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leaves;
use App\Models\Permission;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeDashboardController extends Controller
{
    // ----------------------------------------------------------
    // HELPERS (identik dengan Api\Employee\EmployeeAttendanceController
    // supaya angka yang ditampilkan di web sama persis dengan mobile)
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

    // ----------------------------------------------------------
    // GET /employee/dashboard
    // ----------------------------------------------------------
    public function index(): View
    {
        $this->ensureEmployee();

        $user      = $this->me();
        $company   = $this->companyOrFail();
        $today     = Carbon::today()->toDateString();

        $attendance = Attendance::where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $shift        = $this->resolveShift($user, $today);
        $scheduledIn  = $this->getScheduledIn($shift, $company, $today);
        $scheduledOut = $this->getScheduledOut($shift, $company, $today);

        // ----- Ringkasan bulan berjalan (logika sama dengan API summary()) -----
        $month = (int) Carbon::now()->month;
        $year  = (int) Carbon::now()->year;
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $monthAttendances = Attendance::where('company_id', $company->id)
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get(['date', 'status']);

        $summary = [
            'hadir'     => $monthAttendances->whereIn('status', ['on_time', 'overtime', 'guest'])->count(),
            'terlambat' => $monthAttendances->where('status', 'late')->count(),
            'alpha'     => $monthAttendances->where('status', 'absent')->count(),
            'izin'      => Permission::where('user_id', $user->id)
                ->where('company_id', $company->id)
                ->whereBetween('date_permission', [$start->toDateString(), $end->toDateString()])
                ->where('is_approved', true)
                ->count(),
            'cuti'      => Leaves::where('user_id', $user->id)
                ->where('company_id', $company->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', $end->toDateString())
                ->where('end_date', '>=', $start->toDateString())
                ->count(),
        ];

        // ----- Menu cepat — status "aktif" hanya untuk modul yang sudah dibuatkan halaman web-nya -----
     $tenantType = app('tenant')->type;
$routePrefix = $tenantType . '.member.';

$menu = [
    ['label' => 'Riwayat Absensi', 'route' => $routePrefix . 'attendance.index', 'active' => true],
    ['label' => 'Ajukan Izin', 'route' => $routePrefix . 'permissions.index', 'active' => true],
    ['label' => 'Ajukan Cuti', 'route' => $routePrefix . 'leaves.index', 'active' => true],
    ['label' => 'Lembur', 'route' => $routePrefix . 'overtimes.index', 'active' => true],
    ['label' => 'Pinjaman', 'route' => null, 'active' => false],
    ['label' => 'Jadwal Shift', 'route' => $routePrefix . 'shifts.schedule', 'active' => true],
    ['label' => 'Slip Gaji', 'route' => $routePrefix . 'payrolls.index', 'active' => true],
    ['label' => 'Performa', 'route' => $routePrefix . 'performance-scores.index', 'active' => true],
    ['label' => 'Laporan Harian', 'route' => $routePrefix . 'daily-reports.index', 'active' => true],
    ['label' => 'Laporan Bulanan', 'route' => $routePrefix . 'monthly-reports.index', 'active' => true],
    ['label' => 'Catatan dari HR', 'route' => $routePrefix . 'notes.index', 'active' => true],
    ['label' => 'Hari Libur', 'route' => $routePrefix . 'holidays.index', 'active' => true],
];

        return view('pages.employee.dashboard', [
            'user'         => $user,
            'company'      => $company,
            'attendance'   => $attendance,
            'shift'        => $shift,
            'scheduledIn'  => $scheduledIn,
            'scheduledOut' => $scheduledOut,
            'summary'      => $summary,
            'menu'         => $menu,
            'today'        => $today,
        ]);
    }
}