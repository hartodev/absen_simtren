<?php

namespace App\Http\Controllers\Web\Tenant\Legacy;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Siswa;

/**
 * LEGACY -- tidak terdaftar di routes/web.php (sisa desain lama). Aman dihapus
 * kalau sudah pasti tidak dipakai; view-nya ada di resources/views/_legacy.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $tenant = app('currentTenant');
        $user = auth()->user();

        if ($tenant->tipe_lembaga === 'company') {
            $totalAnggota = Karyawan::where('status_aktif', true)->count();
            $hadirHariIni = AbsensiKaryawan::whereDate('tanggal', now())
                ->whereIn('status', ['hadir', 'telat', 'lembur'])
                ->count();
            $tidakHadir = AbsensiKaryawan::whereDate('tanggal', now())
                ->whereIn('status', ['izin', 'sakit', 'alpa'])
                ->count();

            $absensiTerbaru = AbsensiKaryawan::with('karyawan')
                ->whereDate('tanggal', now())
                ->latest('updated_at')
                ->take(5)
                ->get();

            return view('_legacy.tenant.dashboard', compact(
                'tenant', 'user', 'totalAnggota', 'hadirHariIni', 'tidakHadir', 'absensiTerbaru'
            ));
        }

        $totalAnggota = Siswa::count();
        $hadirHariIni = Presensi::whereDate('tanggal', now())->where('status', 'hadir')->count();
        $tidakHadir = Presensi::whereDate('tanggal', now())->whereIn('status', ['izin', 'sakit', 'alpa'])->count();

        return view('_legacy.tenant.dashboard', compact('tenant', 'user', 'totalAnggota', 'hadirHariIni', 'tidakHadir'));
    }
}