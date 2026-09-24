<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\MonthlyReport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class EmployeeMonthlyReportController extends Controller
{
    private function uploadAttachment($file): string
    {
        $destinationPath = public_path('image/monthly-reports');
        if (! File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $fileName);
        return 'image/monthly-reports/' . $fileName;
    }

    private function deleteAttachment(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    private function findOwned(int $id): MonthlyReport
    {
        return MonthlyReport::with('approver:id,name')
            ->where('company_id', Auth::user()->company_id)
            ->where('user_id', Auth::id())
            ->findOrFail($id);
    }

    // ----------------------------------------------------------
    // GET /employee/monthly-reports
    // ----------------------------------------------------------
    public function index(Request $request): View
    {
        $query = MonthlyReport::with('approver:id,name')
            ->where('company_id', Auth::user()->company_id)
            ->where('user_id', Auth::id());

        if ($request->filled('year'))   $query->where('year', $request->year);
        if ($request->filled('status')) $query->where('status', $request->status);

        $reports = $query->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(15)
            ->withQueryString();

        $summaryQuery = MonthlyReport::where('company_id', Auth::user()->company_id)
            ->where('user_id', Auth::id());
        if ($request->filled('year')) $summaryQuery->where('year', $request->year);
        $all = $summaryQuery->get();

        $summary = [
            'total'     => $all->count(),
            'approved'  => $all->where('status', 'approved')->count(),
            'rejected'  => $all->where('status', 'rejected')->count(),
            'submitted' => $all->where('status', 'submitted')->count(),
            'draft'     => $all->where('status', 'draft')->count(),
            'avg_score' => round($all->where('status', 'approved')->avg('score') ?? 0, 2),
        ];

        return view('pages.employee.monthly_report.index', [
            'reports' => $reports,
            'summary' => $summary,
            'filters' => $request->only(['year', 'status']),
        ]);
    }

    // ----------------------------------------------------------
    // GET /employee/monthly-reports/create
    // ----------------------------------------------------------
    public function create(): View
    {
        return view('pages.employee.monthly_report.create');
    }

    // ----------------------------------------------------------
    // POST /employee/monthly-reports
    // ----------------------------------------------------------
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month'       => ['required', 'integer', 'between:1,12'],
            'year'        => ['required', 'integer'],
            'target'      => ['required', 'string'],
            'achievement' => ['required', 'string'],
            'problem'     => ['required', 'string'],
            'solution'    => ['required', 'string'],
            'attachment'  => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $exists = MonthlyReport::where('company_id', Auth::user()->company_id)
            ->where('user_id', Auth::id())
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Laporan bulan ' . $validated['month'] . '/' . $validated['year'] . ' sudah ada.');
        }

        $data = array_merge($validated, [
            'company_id' => Auth::user()->company_id,
            'user_id'    => Auth::id(),
            'status'     => 'draft',
        ]);
        unset($data['attachment']);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $this->uploadAttachment($request->file('attachment'));
        }

        $report = MonthlyReport::create($data);

        return redirect()->route('company.member.monthly-reports.show', $report->id)
            ->with('success', 'Laporan berhasil dibuat sebagai draft.');
    }

    // ----------------------------------------------------------
    // GET /employee/monthly-reports/{id}
    // ----------------------------------------------------------
    public function show(int $id): View
    {
        return view('pages.employee.monthly_report.show', [
            'report' => $this->findOwned($id),
        ]);
    }

    // ----------------------------------------------------------
    // GET /employee/monthly-reports/{id}/edit
    // ----------------------------------------------------------
    public function edit(int $id): View|RedirectResponse
    {
        $report = $this->findOwned($id);

        if (! in_array($report->status, ['draft', 'rejected'], true)) {
            return redirect()->route('company.member.monthly-reports.show', $id)
                ->with('error', 'Laporan yang sudah disubmit / diapprove tidak bisa diedit.');
        }

        return view('pages.employee.monthly_report.edit', ['report' => $report]);
    }

    // ----------------------------------------------------------
    // POST /employee/monthly-reports/{id}
    // ----------------------------------------------------------
    public function update(Request $request, int $id): RedirectResponse
    {
        $report = $this->findOwned($id);

        if (! in_array($report->status, ['draft', 'rejected'], true)) {
            return back()->with('error', 'Laporan yang sudah disubmit / diapprove tidak bisa diedit.');
        }

        $validated = $request->validate([
            'target'      => ['sometimes', 'string'],
            'achievement' => ['sometimes', 'string'],
            'problem'     => ['sometimes', 'string'],
            'solution'    => ['sometimes', 'string'],
            'attachment'  => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $data = array_filter($validated, fn ($v) => $v !== null);
        unset($data['attachment']);

        if ($request->hasFile('attachment')) {
            $this->deleteAttachment($report->attachment);
            $data['attachment'] = $this->uploadAttachment($request->file('attachment'));
        }

        if ($report->status === 'rejected') {
            $data['status']      = 'draft';
            $data['approved_by'] = null;
            $data['approved_at'] = null;
            $data['score']       = 0;
        }

        $report->update($data);

        return redirect()->route('company.member.monthly-reports.show', $id)->with('success', 'Laporan berhasil diupdate.');
    }

    // ----------------------------------------------------------
    // POST /employee/monthly-reports/{id}/submit
    // ----------------------------------------------------------
    public function submit(int $id): RedirectResponse
    {
        $report = $this->findOwned($id);

        if ($report->status !== 'draft') {
            return back()->with('error', 'Hanya laporan berstatus draft yang bisa disubmit.');
        }

        $report->update(['status' => 'submitted']);

        return redirect()->route('company.member.monthly-reports.show', $id)->with('success', 'Laporan berhasil disubmit ke HR.');
    }

    // ----------------------------------------------------------
    // POST /employee/monthly-reports/{id}/delete
    // ----------------------------------------------------------
    public function destroy(int $id): RedirectResponse
    {
        $report = $this->findOwned($id);

        if ($report->status !== 'draft') {
            return back()->with('error', 'Hanya laporan berstatus draft yang bisa dihapus.');
        }

        $this->deleteAttachment($report->attachment);
        $report->delete();

        return redirect()->route('company.member.monthly-reports.index')->with('success', 'Laporan berhasil dihapus.');
    }
}