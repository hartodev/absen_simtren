<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\MutabaahYaumiyah;
use App\Models\Permission;
use App\Models\User;
use App\Support\SchoolModules;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Beranda admin untuk tenant pesantren & school (dipanggil dari
 * HrDashboardController::index supaya route dashboard yang lama tetap dipakai).
 *
 *   simple (hijau, TPQ)                    -> pages.tenant.admin.simple
 *   full   (biru, sekolah / pondok)        -> pages.tenant.admin.full
 */
class MobileDashboardController extends TenantAdminController
{
    public function show(Company $company)
    {
        if ($company->dashboardStyle() === Company::DASHBOARD_SIMPLE) {
            return $this->view('pages.tenant.admin.simple', $this->simpleData($company));
        }

        return $this->view('pages.tenant.admin.full', [
            'modules' => SchoolModules::forCompany($company),
        ]);
    }

    private function simpleData(Company $company): array
    {
        $me    = Auth::user();
        $today = Carbon::today();
        $cid   = $company->id;

        // ---- Kehadiran ustadz sendiri bulan ini ----
        $monthAtt = Attendance::where('company_id', $cid)->where('user_id', $me->id)
            ->whereYear('date', $today->year)->whereMonth('date', $today->month)->get();

        $telat = $monthAtt->where('status', 'late')->count();
        $hadir = $monthAtt->whereNotNull('time_in')->where('status', '!=', 'late')->count();
        $izin  = Permission::where('company_id', $cid)->where('user_id', $me->id)->where('is_approved', true)
            ->whereYear('date_permission', $today->year)->whereMonth('date_permission', $today->month)->count();

        // hari kerja yang sudah lewat (Senin-Sabtu, hari ini tidak dihitung)
        $workdays = 0;
        for ($d = $today->copy()->startOfMonth(); $d->lt($today); $d->addDay()) {
            if (! $d->isSunday()) {
                $workdays++;
            }
        }
        $absen = max(0, $workdays - $hadir - $telat - $izin);

        $todayAtt = $monthAtt->first(fn ($a) => Carbon::parse($a->date)->isSameDay($today));

        // ---- Mutaba'ah hari ini ----
        $mutabaah = MutabaahYaumiyah::where('company_id', $cid)->hariIni();
        $totalSesi = (clone $mutabaah)->count();
        $sudah     = (clone $mutabaah)->whereNotNull('signed_by')->count();

        // ---- Izin pending (santri -> tabel permissions) ----
        $pending = Permission::with('user:id,name')->where('company_id', $cid)->whereNull('is_approved')
            ->latest('id')->limit(3)->get();

        // ---- Jadwal kegiatan hari ini (tabel schedules milik user) ----
        $schedules = collect();
        if (Schema::hasTable('schedules')) {
            $schedules = DB::table('schedules')->where('user_id', $me->id)
                ->whereDate('start_datetime', $today)->orderBy('start_datetime')->limit(5)->get();
        }

        return [
            'me'          => $me,
            'attToday'    => $todayAtt,
            'att'         => ['hadir' => $hadir, 'telat' => $telat, 'absen' => $absen, 'izin' => $izin],
            'mutabaah'    => ['total' => $totalSesi, 'belum' => $totalSesi - $sudah, 'sudah' => $sudah,
                              'persen' => $totalSesi ? round($sudah / $totalSesi * 100) : 0],
            'pending'     => $pending,
            'schedules'   => $schedules,
            'summary'     => [
                'santri' => User::where('company_id', $cid)->where('role', 'santri')->count(),
                'hadir'  => Attendance::where('company_id', $cid)->whereYear('date', $today->year)
                    ->whereMonth('date', $today->month)->whereNotNull('time_in')->count(),
                'sesi'   => MutabaahYaumiyah::where('company_id', $cid)->bulan($today->month, $today->year)->count(),
            ],
            'monthLabel'  => $today->translatedFormat('F Y'),
        ];
    }
}
