<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class EmployeeDailyReportController extends Controller
{
    private function uploadAttachment($file): string
    {
        $destinationPath = public_path('image/daily-reports');
        if (! File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $fileName);
        return 'image/daily-reports/' . $fileName;
    }

    private function deleteAttachment(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    // ----------------------------------------------------------
    // GET /employee/daily-reports
    // ----------------------------------------------------------
    public function index(Request $request): View
    {
        $companyId = Auth::user()->company_id;
        $userId    = Auth::id();
        $today     = now()->toDateString();

        $todayReport = DailyReport::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->where('date', $today)
            ->first();

        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year', now()->year);

        $reports = DailyReport::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderByDesc('date')
            ->paginate(15)
            ->withQueryString();

        // Ringkasan bulan dihitung dari full query (bukan hasil paginate)
        $monthReports = DailyReport::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get(['is_achieved', 'achievement']);

        $summary = [
            'total_days'     => $monthReports->count(),
            'achieved'       => $monthReports->where('is_achieved', true)->count(),
            'not_achieved'   => $monthReports->where('is_achieved', false)->whereNotNull('achievement')->count(),
            'pending_evening' => $monthReports->whereNull('achievement')->count(),
        ];

        return view('pages.employee.daily_report.index', [
            'todayReport' => $todayReport,
            'reports'     => $reports,
            'summary'     => $summary,
            'month'       => $month,
            'year'        => $year,
            'today'       => $today,
        ]);
    }

    // ----------------------------------------------------------
    // GET /employee/daily-reports/{id}
    // ----------------------------------------------------------
public function show($id): View
{
    $id = (int) $id;

    $report = DailyReport::where('company_id', Auth::user()->company_id)
        ->where('user_id', Auth::id())
        ->findOrFail($id);

    return view('pages.employee.daily_report.show', [
        'report' => $report,
    ]);
}

    // ----------------------------------------------------------
    // POST /employee/daily-reports — submit target pagi
    // ----------------------------------------------------------
    public function store(Request $request): RedirectResponse
    {
        $companyId = Auth::user()->company_id;
        $userId    = Auth::id();
        $today     = now()->toDateString();

        $exists = DailyReport::where('company_id', $companyId)
            ->where('user_id', $userId)
            ->where('date', $today)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kamu sudah submit target hari ini.');
        }

        $validated = $request->validate([
            'target'     => ['required', 'string'],
            'attachment' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $data = [
            'company_id' => $companyId,
            'user_id'    => $userId,
            'date'       => $today,
            'target'     => $validated['target'],
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $this->uploadAttachment($request->file('attachment'));
        }

        DailyReport::create($data);

        return redirect()->route('company.member.daily-reports.index')->with('success', 'Target pagi berhasil disubmit.');
    }

// ----------------------------------------------------------
// PUT /employee/daily-reports — submit pencapaian sore (untuk laporan hari ini)
// ----------------------------------------------------------
public function update(Request $request): RedirectResponse
{
    $companyId = Auth::user()->company_id;
    $userId    = Auth::id();
    $today     = now()->toDateString();

    $report = DailyReport::where('company_id', $companyId)
        ->where('user_id', $userId)
        ->where('date', $today)
        ->firstOrFail();

    $validated = $request->validate([
        'achievement'         => ['required', 'string'],
        'is_achieved'         => ['required', 'boolean'],
        'reason_not_achieved' => ['nullable', 'string', 'required_if:is_achieved,0'],
        'attachment'          => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
    ]);

    $data = [
        'achievement'         => $validated['achievement'],
        'is_achieved'         => (bool) $validated['is_achieved'],
        'reason_not_achieved' => $validated['reason_not_achieved'] ?? null,
    ];

    if ($request->hasFile('attachment')) {
        $this->deleteAttachment($report->attachment);
        $data['attachment'] = $this->uploadAttachment($request->file('attachment'));
    }

    $report->update($data);

    return redirect()->route('company.member.daily-reports.index')->with('success', 'Pencapaian sore berhasil disubmit.');
}
}