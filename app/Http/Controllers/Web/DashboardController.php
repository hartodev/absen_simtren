<?php

namespace App\Http\Controllers\Web;

use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Siswa;

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

            return view('tenant.dashboard', compact(
                'tenant', 'user', 'totalAnggota', 'hadirHariIni', 'tidakHadir', 'absensiTerbaru'
            ));
        }

        $totalAnggota = Siswa::count();
        $hadirHariIni = Presensi::whereDate('tanggal', now())->where('status', 'hadir')->count();
        $tidakHadir = Presensi::whereDate('tanggal', now())->whereIn('status', ['izin', 'sakit', 'alpa'])->count();

        return view('tenant.dashboard', compact('tenant', 'user', 'totalAnggota', 'hadirHariIni', 'tidakHadir'));
    }
}