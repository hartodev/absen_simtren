<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leaves;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmployeeLeaveController extends Controller
{
    // ----------------------------------------------------------
    // GET /employee/leaves
    // ----------------------------------------------------------
    public function index(Request $request): View
    {
        $user = $request->user();

        $q = Leaves::query()
            ->where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->with(['approver:id,name'])
            ->orderByDesc('start_date')
            ->orderByDesc('id');

        if ($request->filled('status')) $q->where('status', $request->status);

        $leaves = $q->paginate(15)->withQueryString();

        return view('pages.employee.leaves.index', [
            'leaves'  => $leaves,
            'filters' => $request->only(['status']),
        ]);
    }

    // ----------------------------------------------------------
    // GET /employee/leaves/create
    // ----------------------------------------------------------
    public function create(): View
    {
        return view('pages.employee.leaves.create');
    }

    // ----------------------------------------------------------
    // POST /employee/leaves
    // ----------------------------------------------------------
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'type'       => ['nullable', 'in:annual,sick,maternity,important,other'],
            'reason'     => ['nullable', 'string'],
        ]);

        $leave = Leaves::create([
            'company_id' => $user->company_id,
            'user_id'    => $user->id,
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
            'type'       => $validated['type'] ?? 'annual',
            'reason'     => $validated['reason'] ?? null,
            'status'     => 'pending',
        ]);

        return redirect()->route('company.member.leaves.show', $leave->id)
            ->with('success', 'Pengajuan cuti berhasil dikirim, menunggu persetujuan HR.');
    }

    // ----------------------------------------------------------
    // GET /employee/leaves/{id}
    // ----------------------------------------------------------
    public function show(Request $request, int $id): View
    {
        $user = $request->user();

        $leave = Leaves::query()
            ->where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->with(['approver:id,name'])
            ->findOrFail($id);

        return view('pages.employee.leaves.show', ['leave' => $leave]);
    }

    // ----------------------------------------------------------
    // POST /employee/leaves/{id}/cancel
    // ----------------------------------------------------------
    public function cancel(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();

        $leave = Leaves::query()
            ->where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->findOrFail($id);

        if ($leave->status !== 'pending') {
            return back()->with('error', 'Hanya cuti berstatus pending yang bisa dibatalkan.');
        }

        $leave->update(['status' => 'canceled']);

        return redirect()->route('company.member.leaves.index')->with('success', 'Pengajuan cuti dibatalkan.');
    }
}