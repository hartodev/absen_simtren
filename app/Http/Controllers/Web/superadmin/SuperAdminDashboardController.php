<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        $totalTenants = Company::count();
        $totalAktif = Company::where('status', 'aktif')->count();
        $totalPending = Company::where('status', 'pending')->count();
        $totalUsers = User::count();
        $byType = Company::selectRaw('type, count(*) as jumlah')->groupBy('type')->pluck('jumlah', 'type');

        return view('pages.admin.dashboard', compact('totalTenants', 'totalAktif', 'totalPending', 'totalUsers', 'byType'));
    }
}
