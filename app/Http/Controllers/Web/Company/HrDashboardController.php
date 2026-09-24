<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class HrDashboardController extends Controller
{
    // ----------------------------------------------------------
    // HELPERS
    // (mengikuti pattern Api\Employee\*Controller: ensureX / companyOrFail)
    // ----------------------------------------------------------

    private function ensureHr(): void
    {
        // Controller ini dipakai bareng oleh 3 tipe tenant (company/pesantren/school)
        // — lihat routes/web.php. Cek generik "admin organisasi", tipe-nya sendiri
        // sudah dijamin cocok oleh middleware 'tenant.type:{type}' + 'role:{adminRole}'.
        if (! Auth::check() || ! in_array(Auth::user()->role, ['hr', 'ustadz', 'teacher'], true)) {
            abort(403, 'Akses ditolak (khusus admin organisasi).');
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

    /**
     * Role anggota (bukan admin) sesuai tipe tenant — samakan dengan
     * HrAttendanceController::memberRole() / HrEmployeeController::memberRole().
     */
    private function memberRole(string $companyType): string
    {
        return match ($companyType) {
            'pesantren' => 'santri',
            'school'    => 'student',
            default     => 'employee',
        };
    }

    // ----------------------------------------------------------
    // GET /company/dashboard
    // Halaman penuh (dipanggil saat load pertama / reload browser)
    // ----------------------------------------------------------
    public function index()
    {
        $this->ensureHr();

        $company = $this->companyOrFail();

        // Pesantren & sekolah punya beranda web sendiri (hijau TPQ / biru grid modul).
        if (in_array($company->type, ['pesantren', 'school'], true)) {
            return app(\App\Http\Controllers\Web\Tenant\AdminDashboardController::class)->show($company);
        }

        return view('pages.company.dashboard', [
            'company' => $company,
        ] + $this->dashboardData($company));
    }

    // ----------------------------------------------------------
    // GET /company/dashboard/refresh
    // Dipanggil via AJAX oleh tombol refresh (ikon ↻ di header),
    // hanya mengembalikan partial dashboard_content, bukan full page.
    // ----------------------------------------------------------
    public function refresh(): View
    {
        $this->ensureHr();

        $company = $this->companyOrFail();

        return view('pages.company.dashboard_content', [
            'company' => $company,
        ] + $this->dashboardData($company));
    }

    // ----------------------------------------------------------
    // DATA BUILDER
    // ----------------------------------------------------------

    private function dashboardData($company): array
    {
        return [
            'stats'              => $this->buildStats($company),
            'recentAttendances'  => $this->recentAttendances($company->id),
            'pendingPermissions' => $this->pendingPermissions($company->id),
        ];
    }

    /**
     * Kartu statistik atas: Total Karyawan, Hadir Hari Ini, Izin Pending, Terlambat.
     */
    private function buildStats($company): array
    {
        $today = Carbon::today()->toDateString();
        $companyId = $company->id;

        $totalKaryawan = User::where('company_id', $companyId)
            ->where('role', $this->memberRole($company->type))
            ->count();

        $hadirHariIni = Attendance::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->whereNotNull('time_in')
            ->count();

        // "Izin Pending" = is_approved masih null (lihat EmployeePermissionController::store)
        $izinPending = Permission::where('company_id', $companyId)
            ->whereNull('is_approved')
            ->count();

        $terlambat = Attendance::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->where('status', 'late')
            ->count();

        return [
            'total_karyawan' => $totalKaryawan,
            'hadir_hari_ini' => $hadirHariIni,
            'izin_pending'   => $izinPending,
            'terlambat'      => $terlambat,
        ];
    }

    /**
     * List "Absensi Terbaru" — karyawan yang sudah check-in hari ini,
     * diurutkan dari yang paling baru check-in.
     */
    private function recentAttendances(int $companyId, int $limit = 5)
    {
        $today = Carbon::today()->toDateString();

        return Attendance::with('user:id,name,email')
            ->where('company_id', $companyId)
            ->whereDate('date', $today)
            ->whereNotNull('time_in')
            ->orderByDesc('time_in')
            ->limit($limit)
            ->get()
            ->map(fn (Attendance $a) => [
                'id'          => $a->id,
                'name'        => $a->user->name ?? '-',
                'time_in'     => $a->time_in,
                'checked_out' => (bool) $a->time_out,
            ]);
    }

    /**
     * List "Izin Menunggu Approval" — permission dengan is_approved masih null.
     */
    private function pendingPermissions(int $companyId, int $limit = 5)
    {
        return Permission::with('user:id,name')
            ->where('company_id', $companyId)
            ->whereNull('is_approved')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (Permission $p) => [
                'id'     => $p->id,
                'name'   => $p->user->name ?? '-',
                'reason' => $p->reason,
                'date'   => $p->date_permission,
            ]);
    }
}