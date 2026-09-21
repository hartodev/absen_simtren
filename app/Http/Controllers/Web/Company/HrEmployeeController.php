<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Kelola anggota organisasi (karyawan / santri / siswa -- tergantung
 * tipe tenant). Satu controller dipakai bersama oleh ketiga tipe lembaga,
 * sama seperti HrDashboardController & HrAttendanceController -- yang
 * membedakan cuma role member-nya (employee / santri / student), dan
 * itu ditentukan otomatis dari tipe tenant yang sedang diakses.
 */
class HrEmployeeController extends Controller
{
    /**
     * role member sesuai tipe tenant yang sedang aktif di subdomain ini.
     */
    private function memberRole(): string
    {
        return match (app('tenant')->type) {
            'pesantren' => 'santri',
            'school' => 'student',
            default => 'employee',
        };
    }

    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $employees = User::where('company_id', $companyId)
            ->where('role', $this->memberRole())
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('pages.companies.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('pages.companies.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $this->memberRole(),
            'company_id' => $request->user()->company_id,
        ]);

        return redirect(url('/' . request()->segment(1) . '/employees'))
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(Request $request, $tenant, $id)
    {
        $employee = User::where('company_id', $request->user()->company_id)
            ->where('role', $this->memberRole())
            ->findOrFail($id);

        return view('pages.companies.employees.edit', compact('employee'));
    }

    public function update(Request $request, $tenant, $id)
    {
        $employee = User::where('company_id', $request->user()->company_id)
            ->where('role', $this->memberRole())
            ->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($employee->id)],
            'password' => 'nullable|min:6|confirmed',
        ]);

        $employee->name = $request->name;
        $employee->email = $request->email;
        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }
        $employee->save();

        return redirect(url('/' . request()->segment(1) . '/employees'))
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Request $request, $tenant, $id)
    {
        $employee = User::where('company_id', $request->user()->company_id)
            ->where('role', $this->memberRole())
            ->findOrFail($id);

        $employee->delete();

        return back()->with('success', 'Anggota berhasil dihapus.');
    }
}
