<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class HrAttendanceController extends Controller
{
    /**
     * GET /company/attendances
     *
     * Daftar absensi SELURUH karyawan di tenant ini (bukan cuma milik
     * sendiri seperti di role Employee). Mengisi $attendances yang dipakai
     * langsung oleh resources/views/pages/companies/attendances/index.blade.php.
     */
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $attendances = Attendance::where('company_id', $companyId)
            ->with('user')
            ->when($request->filled('name'), function ($q) use ($request) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%' . $request->query('name') . '%'));
            })
            ->orderByDesc('date')
            ->paginate(20)
            ->withQueryString();

        return view('pages.companies.attendances.index', compact('attendances'));
    }

    /**
     * GET /company/attendances/{id}
     *
     * Detail satu record absensi — HR bisa buka detail absensi karyawan
     * manapun di tenant-nya (bukan cuma milik sendiri).
     */
    public function show(Request $request, $tenant, $id)
    {
        $companyId = $request->user()->company_id;

        $attendance = Attendance::where('company_id', $companyId)
            ->with(['user', 'shift', 'markedBy'])
            ->findOrFail($id);

        return view('pages.companies.attendances.show', compact('attendance'));
    }
}
