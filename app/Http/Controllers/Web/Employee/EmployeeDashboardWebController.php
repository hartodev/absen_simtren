<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeDashboardWebController extends Controller
{
    /**
     * GET /employee/dashboard  (di dalam subdomain tenant)
     *
     * Halaman pertama yang dilihat karyawan/employee setelah login lewat
     * subdomain tenant-nya. Menyiapkan array $dashboard yang dipakai
     * langsung oleh resources/views/pages/employee/dashboard.blade.php
     * (view ini sudah ada, controllernya yang tadinya belum ada).
     */
    public function index(Request $request)
    {
        $user      = $request->user();
        $companyId = $user->company_id;
        $now       = Carbon::now();

        $monthAttendances = Attendance::where('company_id', $companyId)
            ->where('user_id', $user->id)
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->get();

        $hadir     = $monthAttendances->whereNotNull('time_in')->count();
        $terlambat = $monthAttendances->where('status', 'late')->count();
        $alpha     = $monthAttendances->where('status', 'absent')->count();

        $izinPending = Permission::where('company_id', $companyId)
            ->where('user_id', $user->id)
            ->where('is_approved', false)
            ->whereMonth('date_permission', $now->month)
            ->whereYear('date_permission', $now->year)
            ->count();

        $dashboard = [
            'user'         => $user,
            'hadir'        => $hadir,
            'terlambat'    => $terlambat,
            'alpha'        => $alpha,
            'izin_pending' => $izinPending,
        ];

        return view('pages.employee.dashboard', compact('dashboard'));
    }
}
