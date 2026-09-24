<?php

namespace App\Http\Controllers\Web\Employee\Legacy;

use App\Http\Controllers\Controller;
use App\Models\Payrool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * LEGACY -- tidak terdaftar di routes/web.php (sisa desain lama). Aman dihapus
 * kalau sudah pasti tidak dipakai; view-nya ada di resources/views/_legacy.
 */
class EmployeePayroleController extends Controller
{
    private function ensureEmployee(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'employee') {
            abort(403, 'Akses ditolak (khusus employee).');
        }
    }

    private function companyId(): int
    {
        $companyId = Auth::user()->company_id ?? null;
        if (! $companyId) {
            abort(422, 'Company ID tidak ditemukan.');
        }
        return (int) $companyId;
    }

    // ----------------------------------------------------------
    // GET /employee/payrolls
    // ----------------------------------------------------------
    public function index(Request $request): View
    {
        $this->ensureEmployee();

        $q = Payrool::query()->where('user_id', Auth::id());

        if (Schema::hasColumn('payrools', 'company_id')) {
            $q->where('company_id', $this->companyId());
        }

        if ($request->filled('status')) $q->where('status', $request->status);
        if ($request->filled('from'))   $q->whereDate('period_start', '>=', $request->from);
        if ($request->filled('to'))     $q->whereDate('period_end', '<=', $request->to);

        $payrolls = $q->orderByDesc('id')->paginate(15)->withQueryString();

        return view('pages.employees.payrolls.index', [
            'payrolls' => $payrolls,
            'filters'  => $request->only(['status', 'from', 'to']),
        ]);
    }

    // ----------------------------------------------------------
    // GET /employee/payrolls/{id}
    // ----------------------------------------------------------
    public function show(int $id): View
    {
        $this->ensureEmployee();

        $q = Payrool::query()->where('user_id', Auth::id());
        if (Schema::hasColumn('payrools', 'company_id')) {
            $q->where('company_id', $this->companyId());
        }

        return view('pages.employees.payrolls.show', ['payroll' => $q->findOrFail($id)]);
    }
}