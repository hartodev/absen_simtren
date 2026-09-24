<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Models\StudentAttendance;
use App\Models\StudentPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Izin Siswa (tab "Izin Siswa" di bottom nav biru) -- approve/reject izin & sakit dari wali. */
class StudentPermissionController extends TenantAdminController
{
    public function index(Request $request)
    {
        $status = in_array($request->status, ['pending', 'approved', 'rejected'], true) ? $request->status : 'pending';

        $permissions = StudentPermission::with(['student.classRoom:id,name', 'submitter:id,name'])
            ->whereHas('student', fn ($q) => $q->where('company_id', $this->cid()))
            ->where('status', $status)
            ->orderByDesc('date_permission')->orderByDesc('id')
            ->paginate(15)->withQueryString();

        $counts = StudentPermission::whereHas('student', fn ($q) => $q->where('company_id', $this->cid()))
            ->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');

        return $this->view('permissions.index', compact('permissions', 'status', 'counts'));
    }

    public function review(Request $request, int $id)
    {
        $data = $request->validate(['action' => ['required', 'in:approve,reject']]);

        $permission = StudentPermission::with('student')
            ->whereHas('student', fn ($q) => $q->where('company_id', $this->cid()))
            ->findOrFail($id);

        if ($permission->status !== 'pending') {
            return back()->with('error', 'Izin ini sudah diproses.');
        }

        $permission->update([
            'status'      => $data['action'] === 'approve' ? 'approved' : 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Izin disetujui -> otomatis tercatat di rekap absensi hari itu (izin / sakit).
        if ($data['action'] === 'approve' && $permission->student->class_id) {
            StudentAttendance::updateOrCreate(
                ['student_id' => $permission->student_id, 'date' => $permission->date_permission],
                [
                    'company_id'  => $this->cid(),
                    'class_id'    => $permission->student->class_id,
                    'status'      => $permission->type,   // 'izin' | 'sakit'
                    'recorded_by' => Auth::id(),
                    'notes'       => 'Dari pengajuan izin wali',
                ]
            );
        }

        return back()->with('success', $data['action'] === 'approve' ? 'Izin disetujui.' : 'Izin ditolak.');
    }
}
