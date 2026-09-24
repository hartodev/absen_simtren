<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf; // composer require barryvdh/laravel-dompdf
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HrEmployeeController extends Controller
{
    // ----------------------------------------------------------
    // HELPERS
    // Sama seperti HrDashboardController: controller ini dipakai
    // bersama company/pesantren/school, jadi role anggota (employee/
    // santri/student) diturunkan dari tipe tenant, bukan hardcode.
    // ----------------------------------------------------------

    private const ADMIN_ROLES = ['hr', 'ustadz', 'teacher'];

    private function ensureAdmin(): void
    {
        if (! Auth::check() || ! in_array(Auth::user()->role, self::ADMIN_ROLES, true)) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function tenantOrFail()
    {
        if (app()->bound('tenant')) {
            return app('tenant');
        }

        $company = Auth::user()->company;

        if (! $company) {
            abort(422, 'Tenant tidak ditemukan untuk user ini.');
        }

        return $company;
    }

    // ----------------------------------------------------------
    // Role user untuk "anggota" tenant ini: employee (company),
    // santri (pesantren), student (school). Dipakai untuk query
    // DAN untuk isi kolom `role` waktu store().
    // ----------------------------------------------------------
    private function memberRole(): string
    {
        return match ($this->tenantOrFail()->type) {
            'pesantren' => 'santri',
            'school'    => 'student',
            default     => 'employee',
        };
    }

    private function baseQuery(int $companyId)
    {
        return User::where('company_id', $companyId)->where('role', $this->memberRole());
    }

    // ----------------------------------------------------------
    // LIST + SEARCH
    // GET /{type}/employees  (route: {type}.employees.index)
    // ----------------------------------------------------------
    public function index(Request $request)
    {
        $this->ensureAdmin();
        $company = $this->tenantOrFail();

        $q = trim((string) $request->query('q', ''));

        $karyawan = $this->baseQuery($company->id)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('pages.company.employees.index', [
            'company'  => $company,
            'karyawan' => $karyawan,
            'q'        => $q,
        ]);
    }

    // ----------------------------------------------------------
    // FORM TAMBAH
    // GET /{type}/employees/create  (route: {type}.employees.create)
    // ----------------------------------------------------------
    public function create(Request $request)
    {
        $this->ensureAdmin();
        $company = $this->tenantOrFail();

        return view('pages.company.employees.create');
    }

    // ----------------------------------------------------------
    // SIMPAN
    // POST /{type}/employees  (route: {type}.employees.store)
    // ----------------------------------------------------------
    public function store(Request $request)
    {
        $this->ensureAdmin();
        $company = $this->tenantOrFail();

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone'      => ['nullable', 'string', 'max:30'],
            'department' => ['required', 'string', 'max:100'],
            'position'   => ['required', 'string', 'max:100'],
            'password'   => ['nullable', 'string', 'min:8'],
        ]);

        $plainPassword = $validated['password'] ?? Str::random(10);

        User::create([
            'company_id' => $company->id,
            'role'       => $this->memberRole(),
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'department' => $validated['department'],
            'position'   => $validated['position'],
            'password'   => Hash::make($plainPassword),
        ]);

        return redirect()
            ->route("{$company->type}.employees.index")
            ->with('status', 'Karyawan berhasil ditambahkan.' . (empty($validated['password'])
                ? " Password sementara: {$plainPassword}"
                : ''));
    }

    // ----------------------------------------------------------
    // FORM EDIT
    // GET /{type}/employees/{id}/edit  (route: {type}.employees.edit)
    // ----------------------------------------------------------
    public function edit(Request $request, int $id)
    {
        $this->ensureAdmin();
        $company = $this->tenantOrFail();

        $karyawan = $this->baseQuery($company->id)->findOrFail($id);

        return view('pages.company.employees.edit', [
            'karyawan' => $karyawan,
        ]);
    }

    // ----------------------------------------------------------
    // UPDATE
    // PUT /{type}/employees/{id}  (route: {type}.employees.update)
    // ----------------------------------------------------------
    public function update(Request $request, int $id)
    {
        $this->ensureAdmin();
        $company = $this->tenantOrFail();

        $karyawan = $this->baseQuery($company->id)->findOrFail($id);

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($karyawan->id)],
            'phone'      => ['nullable', 'string', 'max:30'],
            'department' => ['required', 'string', 'max:100'],
            'position'   => ['required', 'string', 'max:100'],
            'password'   => ['nullable', 'string', 'min:8'],
        ]);

        $karyawan->fill([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'department' => $validated['department'],
            'position'   => $validated['position'],
        ]);

        if (! empty($validated['password'])) {
            $karyawan->password = Hash::make($validated['password']);
        }

        $karyawan->save();

        return redirect()
            ->route("{$company->type}.employees.index")
            ->with('status', "{$karyawan->name} berhasil diperbarui.");
    }

    // ----------------------------------------------------------
    // HAPUS
    // DELETE /{type}/employees/{id}  (route: {type}.employees.destroy)
    // ----------------------------------------------------------
    public function destroy(Request $request, int $id)
    {
        $this->ensureAdmin();
        $company = $this->tenantOrFail();

        $karyawan = $this->baseQuery($company->id)->findOrFail($id);
        $karyawan->delete();

        return redirect()
            ->route("{$company->type}.employees.index")
            ->with('status', "{$karyawan->name} berhasil dihapus.");
    }

    // ----------------------------------------------------------
    // EXPORT PDF
    // GET /{type}/employees/export  (route: {type}.employees.export)
    // ----------------------------------------------------------
    public function exportPdf(Request $request)
    {
        $this->ensureAdmin();
        $company = $this->tenantOrFail();

        $q = trim((string) $request->query('q', ''));

        $karyawan = $this->baseQuery($company->id)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('pages.company.employees.pdf', [
            'company'  => $company,
            'karyawan' => $karyawan,
        ])->setPaper('a4', 'portrait');

        $filename = 'karyawan-' . Str::slug($company->name) . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->stream($filename);
    }
}