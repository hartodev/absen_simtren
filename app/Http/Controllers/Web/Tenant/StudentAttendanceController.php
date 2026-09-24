<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Rekap Absensi murid per kelas per tanggal + input/koreksi manual. */
class StudentAttendanceController extends TenantAdminController
{
    public function index(Request $request)
    {
        $classes = ClassRoom::where('company_id', $this->cid())->where('is_active', true)
            ->orderBy('grade_level')->orderBy('name')->get(['id', 'name']);

        $date  = Carbon::parse($request->get('date', today()->toDateString()))->toDateString();
        $class = $classes->firstWhere('id', (int) $request->class_id) ?? $classes->first();

        $students = collect();
        $records  = collect();

        if ($class) {
            $students = Student::where('company_id', $this->cid())
                ->where('class_id', $class->id)->where('is_active', true)->orderBy('name')->get(['id', 'nis', 'name']);

            $records = StudentAttendance::where('company_id', $this->cid())
                ->where('class_id', $class->id)->whereDate('date', $date)
                ->get()->keyBy('student_id');
        }

        $summary = array_fill_keys(StudentAttendance::STATUSES, 0);
        foreach ($records as $r) {
            $summary[$r->status]++;
        }

        return $this->view('attendances.index', compact('classes', 'class', 'date', 'students', 'records', 'summary'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_id'         => ['required', 'integer'],
            'date'             => ['required', 'date'],
            'status'           => ['required', 'array'],
            'status.*'         => ['nullable', 'in:' . implode(',', StudentAttendance::STATUSES)],
        ]);

        $class = ClassRoom::where('company_id', $this->cid())->findOrFail($data['class_id']);
        $ids   = Student::where('company_id', $this->cid())->where('class_id', $class->id)->pluck('id')->all();

        foreach ($data['status'] as $studentId => $status) {
            if (! $status || ! in_array((int) $studentId, $ids, true)) {
                continue; // kosong = tidak diubah; murid dari kelas lain diabaikan
            }

            $row = StudentAttendance::firstOrNew(['student_id' => $studentId, 'date' => $data['date']]);
            $row->company_id  = $this->cid();
            $row->class_id    = $class->id;
            $row->status      = $status;
            $row->recorded_by = Auth::id();

            if (! $row->exists && in_array($status, ['hadir', 'terlambat'], true)) {
                $row->check_in_time = now()->format('H:i:s');
            }
            $row->save();
        }

        return $this->to('student-attendances.index', ['class_id' => $class->id, 'date' => $data['date']])
            ->with('success', 'Absensi tersimpan.');
    }
}
