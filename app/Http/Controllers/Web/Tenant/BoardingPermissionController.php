<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Models\BoardingPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Izin Keluar/Pulang Santri (khusus lembaga beasrama). */
class BoardingPermissionController extends TenantAdminController
{
    public function index(Request $request)
    {
        abort_unless($this->company()->hasBoarding(), 404);

        $status = in_array($request->status, ['pending', 'approved', 'rejected', 'sudah_kembali'], true) ? $request->status : 'pending';

        $permissions = BoardingPermission::with(['student.classRoom:id,name', 'submitter:id,name'])
            ->where('company_id', $this->cid())
            ->where('status', $status)
            ->orderByDesc('tanggal_keluar')->paginate(15)->withQueryString();

        $counts = BoardingPermission::where('company_id', $this->cid())
            ->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');

        return $this->view('boarding.index', compact('permissions', 'status', 'counts'));
    }

    public function review(Request $request, int $id)
    {
        $data = $request->validate([
            'action'         => ['required', 'in:approve,reject,return'],
            'catatan_review' => ['nullable', 'string', 'max:1000'],
        ]);

        $permission = BoardingPermission::where('company_id', $this->cid())->findOrFail($id);

        $allowed = match ($data['action']) {
            'approve', 'reject' => $permission->status === 'pending',
            'return'            => $permission->status === 'approved',
        };
        if (! $allowed) {
            return back()->with('error', 'Status izin ini tidak bisa diubah dengan aksi tersebut.');
        }

        $update = match ($data['action']) {
            'approve' => ['status' => 'approved'],
            'reject'  => ['status' => 'rejected'],
            'return'  => ['status' => 'sudah_kembali', 'tanggal_kembali_aktual' => today()],
        };

        $permission->update($update + [
            'reviewed_by'    => Auth::id(),
            'reviewed_at'    => now(),
            'catatan_review' => $data['catatan_review'] ?? $permission->catatan_review,
        ]);

        return back()->with('success', 'Izin diperbarui.');
    }
}
