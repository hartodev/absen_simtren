<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\CompanyHoliday;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeHolidayController extends Controller
{
    private function ensureEmployee(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'employee') {
            abort(403, 'Akses ditolak (khusus employee).');
        }
    }

    private function companyId()
    {
        return Auth::user()->company_id ?? null;
    }

    private function transformItem(CompanyHoliday $item): array
    {
        $start = Carbon::parse($item->start_date);
        $end   = Carbon::parse($item->end_date);
        $today = now()->toDateString();

        return [
            'id'              => $item->id,
            'start_date'      => $item->start_date,
            'end_date'        => $item->end_date,
            'name'            => $item->name,
            'type'            => $item->type,
            'note'            => $item->note,
            'total_days'      => $start->diffInDays($end) + 1,
            'is_active_today' => $today >= $item->start_date && $today <= $item->end_date,
        ];
    }

    // ----------------------------------------------------------
    // GET /employee/holidays
    // ----------------------------------------------------------
    public function index(Request $request): View
    {
        $this->ensureEmployee();

        $q = CompanyHoliday::query()
            ->where('company_id', $this->companyId())
            ->orderByDesc('start_date')
            ->orderByDesc('end_date');

        if ($request->filled('type')) $q->where('type', $request->type);

        if ($request->filled('q')) {
            $search = trim($request->q);
            $q->where(fn ($sub) => $sub->where('name', 'like', "%{$search}%")
                ->orWhere('note', 'like', "%{$search}%"));
        }

        $holidays = $q->paginate(15)->withQueryString()
            ->through(fn ($item) => $this->transformItem($item));

        return view('pages.employee.holiday.index', [
            'holidays' => $holidays,
            'filters'  => $request->only(['type', 'q']),
        ]);
    }

    // ----------------------------------------------------------
    // GET /employee/holidays/{id}
    // ----------------------------------------------------------
    public function show(int $id): View
    {
        $this->ensureEmployee();

        $item = CompanyHoliday::query()
            ->where('company_id', $this->companyId())
            ->findOrFail($id);

        return view('pages.employee.holiday.show', ['holiday' => $this->transformItem($item)]);
    }
}