<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuperAdminTenantController extends Controller
{
    public function index(Request $request)
    {
        $tenants = Company::query()
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('pages.admin.tenants.create', ['types' => config('organization_types')]);
    }

    public function store(Request $request)
    {
        $reserved = config('reserved_subdomains', []);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(Company::TYPES)],
            'subdomain' => [
                'required', 'alpha_dash', 'max:63', 'unique:companies,subdomain',
                function ($attribute, $value, $fail) use ($reserved) {
                    if (in_array(strtolower($value), $reserved, true)) {
                        $fail('Subdomain ini tidak boleh digunakan.');
                    }
                },
            ],
            'status' => ['required', Rule::in(['pending', 'aktif', 'nonaktif'])],
        ]);

        Company::create($request->only('name', 'type', 'subdomain', 'status'));

        return redirect()->route('superadmin.tenants.index')->with('success', 'Tenant berhasil dibuat.');
    }

    public function show($id)
    {
        $tenant = Company::with('users')->findOrFail($id);
        return view('pages.admin.tenants.show', compact('tenant'));
    }

    public function suspend($id)
    {
        $tenant = Company::findOrFail($id);
        $tenant->update(['status' => 'nonaktif']);
        return back()->with('success', 'Tenant dinonaktifkan.');
    }

    public function activate($id)
    {
        $tenant = Company::findOrFail($id);
        $tenant->update(['status' => 'aktif']);
        return back()->with('success', 'Tenant diaktifkan.');
    }

    public function destroy($id)
    {
        $tenant = Company::findOrFail($id);
        $tenant->delete();
        return redirect()->route('superadmin.tenants.index')->with('success', 'Tenant dihapus.');
    }
}
