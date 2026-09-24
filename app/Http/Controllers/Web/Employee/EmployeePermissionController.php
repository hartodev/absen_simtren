<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class EmployeePermissionController extends Controller
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
        return $companyId;
    }

    // ----------------------------------------------------------
    // GET /employee/permissions
    // ----------------------------------------------------------
    public function index(): View
    {
        $this->ensureEmployee();

        $data = Permission::query()
            ->where('company_id', $this->companyId())
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->paginate(15);

        return view('pages.employee.permissions.index', ['permissions' => $data]);
    }

    // ----------------------------------------------------------
    // GET /employee/permissions/create
    // ----------------------------------------------------------
    public function create(): View
    {
        $this->ensureEmployee();
        return view('pages.employee.permissions.create');
    }

    // ----------------------------------------------------------
    // POST /employee/permissions
    // ----------------------------------------------------------
    public function store(Request $request): RedirectResponse
    {
        $this->ensureEmployee();

        $validated = $request->validate([
            'date_permission' => ['required', 'date'],
            'reason'          => ['required', 'string', 'max:500'],
            'image'           => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $destinationPath = public_path('image/permission');
            if (! File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $file     = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $fileName);
            $imagePath = 'image/permission/' . $fileName;
        }

        $perm = Permission::create([
            'company_id'       => $this->companyId(),
            'user_id'          => Auth::id(),
            'date_permission'  => $validated['date_permission'],
            'reason'           => $validated['reason'],
            'image'            => $imagePath,
            'is_approved'      => null,
        ]);

        return redirect()->route('company.member.permissions.show', $perm->id)
            ->with('success', 'Izin berhasil diajukan, menunggu persetujuan HR.');
    }

    // ----------------------------------------------------------
    // GET /employee/permissions/{id}
    // ----------------------------------------------------------
    public function show(int $id): View
    {
        $this->ensureEmployee();

        $perm = Permission::query()
            ->where('company_id', $this->companyId())
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('pages.employee.permissions.show', ['permission' => $perm]);
    }

    // ----------------------------------------------------------
    // POST /employee/permissions/{id}/cancel
    // ----------------------------------------------------------
    public function cancel(int $id): RedirectResponse
    {
        $this->ensureEmployee();

        $perm = Permission::query()
            ->where('company_id', $this->companyId())
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($perm->is_approved === true) {
            return back()->with('error', 'Tidak bisa dibatalkan, izin sudah disetujui.');
        }

        if ($perm->image && File::exists(public_path($perm->image))) {
            File::delete(public_path($perm->image));
        }

        $perm->delete();

        return redirect()->route('company.member.permissions.index')->with('success', 'Izin berhasil dibatalkan.');
    }
}