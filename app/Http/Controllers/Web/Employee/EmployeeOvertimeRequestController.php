<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\OvertimeRequest;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class EmployeeOvertimeRequestController extends Controller
{
    // ----------------------------------------------------------
    // GET /employee/overtimes
    // ----------------------------------------------------------
    public function index(Request $request): View
    {
        $user = $request->user();

        $q = OvertimeRequest::query()
            ->where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->with(['approver:id,name'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($request->filled('status')) $q->where('status', $request->status);

        $overtimes = $q->paginate(15)->withQueryString();

        return view('pages.employee.overtime.index', [
            'overtimes' => $overtimes,
            'filters'   => $request->only(['status']),
        ]);
    }

    // ----------------------------------------------------------
    // GET /employee/overtimes/create
    // ----------------------------------------------------------
    public function create(Request $request): View
    {
        $user = $request->user();

        // Attendance yang bisa dipilih sebagai rujukan (7 hari terakhir)
        $attendances = Attendance::where('user_id', $user->id)
            ->when(Schema::hasColumn('attendances', 'company_id'), fn ($q) => $q->where('company_id', $user->company_id))
            ->where('date', '>=', Carbon::now()->subDays(7)->toDateString())
            ->orderByDesc('date')
            ->get(['id', 'date']);

        return view('pages.employee.overtime.create', ['attendances' => $attendances]);
    }

    // ----------------------------------------------------------
    // POST /employee/overtimes
    // ----------------------------------------------------------
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'date'            => ['required', 'date'],
            'start_time'      => ['required', 'date_format:H:i'],
            'end_time'        => ['required', 'date_format:H:i'],
            'reason'          => ['nullable', 'string'],
            'attendance_id'   => ['nullable', 'integer', 'exists:attendances,id'],
            'evidence_image'  => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $start = Carbon::createFromFormat('H:i', $validated['start_time']);
        $end   = Carbon::createFromFormat('H:i', $validated['end_time']);
        if ($end->lessThan($start)) $end->addDay();
        $minutes = $start->diffInMinutes($end);

        if ($minutes <= 0) {
            return back()->withInput()->with('error', 'Durasi lembur tidak valid.');
        }

        $exists = OvertimeRequest::query()
            ->where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->whereDate('date', $validated['date'])
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Kamu sudah punya pengajuan lembur pada tanggal tersebut.');
        }

        $attendanceId = $validated['attendance_id'] ?? null;
        if ($attendanceId) {
            $ok = Attendance::where('id', $attendanceId)
                ->where('user_id', $user->id)
                ->when(Schema::hasColumn('attendances', 'company_id'), fn ($q) => $q->where('company_id', $user->company_id))
                ->exists();

            if (! $ok) {
                return back()->withInput()->with('error', 'Absensi yang dipilih tidak valid.');
            }
        }

        $imagePath = null;
        if ($request->hasFile('evidence_image')) {
            $destinationPath = public_path('image/permission');
            if (! File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $file     = $request->file('evidence_image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $fileName);
            $imagePath = 'image/permission/' . $fileName;
        }

        $overtime = OvertimeRequest::create([
            'company_id'     => $user->company_id,
            'user_id'        => $user->id,
            'attendance_id'  => $attendanceId,
            'date'           => $validated['date'],
            'start_time'     => $validated['start_time'],
            'end_time'       => $validated['end_time'],
            'minutes'        => $minutes,
            'reason'         => $validated['reason'] ?? null,
            'evidence_image' => $imagePath,
            'status'         => 'pending',
        ]);

        return redirect()->route('company.member.overtimes.show', $overtime->id)
            ->with('success', 'Pengajuan lembur berhasil dibuat.');
    }

    // ----------------------------------------------------------
    // GET /employee/overtimes/{id}
    // ----------------------------------------------------------
    public function show(Request $request, int $id): View
    {
        $user = $request->user();

        $overtime = OvertimeRequest::query()
            ->where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->with(['attendance', 'approver:id,name'])
            ->findOrFail($id);

        return view('pages.employee.overtime.show', ['overtime' => $overtime]);
    }

    // ----------------------------------------------------------
    // POST /employee/overtimes/{id}/cancel
    // ----------------------------------------------------------
    public function cancel(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();

        $overtime = OvertimeRequest::query()
            ->where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->findOrFail($id);

        if ($overtime->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan lembur status pending yang bisa dibatalkan.');
        }

        $overtime->update(['status' => 'canceled']);

        return redirect()->route('company.member.overtimes.index')->with('success', 'Pengajuan lembur dibatalkan.');
    }
}