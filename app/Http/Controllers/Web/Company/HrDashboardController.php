<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leaves;
use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HrDashboardController extends Controller
{
    /**
     * GET /company/dashboard  (di dalam subdomain tenant)
     *
     * Dashboard HR — mengisi variabel yang dipakai langsung oleh
     * resources/views/pages/companies/dashboard.blade.php (view sudah ada,
     * controllernya yang tadinya belum ada). Logikanya mengikuti
     * App\Http\Controllers\Api\HrCompany\HrCompanyDashboardController
     * (versi mobile) supaya angka yang ditampilkan konsisten di web & app.
     */
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        $today     = Carbon::now()->toDateString();

        $totalEmployees = User::where('company_id', $companyId)
            ->whereNotIn('role', ['hr', 'ustadz', 'teacher'])
            ->count();

        $attendancesToday = Attendance::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->with('user')
            ->get();

        $todayPresent = $attendancesToday->whereIn('status', ['on_time', 'overtime', 'guest'])->count();
        $todayLate    = $attendancesToday->where('status', 'late')->count();

        $todayPermission = Permission::where('company_id', $companyId)
            ->where('is_approved', false)
            ->count();

        // 10 absensi terbaru hari ini, dipakai kartu "Today's Attendance"
        $todayAttendanceList = $attendancesToday
            ->sortByDesc('time_in')
            ->take(10)
            ->values();

        // 5 pengajuan izin terbaru (approved & pending), kartu "Permission Requests"
        $permissionList = Permission::where('company_id', $companyId)
            ->with('user')
            ->latest('date_permission')
            ->limit(5)
            ->get();

        return view('pages.companies.dashboard', compact(
            'totalEmployees',
            'todayPresent',
            'todayLate',
            'todayPermission',
            'todayAttendanceList',
            'permissionList'
        ));
    }
}
