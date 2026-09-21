<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Notifications\CompanyActivationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create()
    {
        $types = config('organization_types');
        return view('pages.auth.register', compact('types'));
    }

    public function store(Request $request)
    {
        $reserved = config('reserved_subdomains', []);
        $types = config('organization_types', []);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys($types)),
            'subdomain' => [
                'required',
                'alpha_dash',
                'max:63',
                'unique:companies,subdomain',
                function ($attribute, $value, $fail) use ($reserved) {
                    if (in_array(strtolower($value), $reserved, true)) {
                        $fail('Subdomain ini tidak boleh digunakan, silakan pilih yang lain.');
                    }
                },
            ],
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        [$company, $admin] = DB::transaction(function () use ($request, $types) {
            $company = Company::create([
                'name' => $request->name,
                'subdomain' => strtolower($request->subdomain),
                'type' => $request->type,
                'status' => 'pending',
            ]);

            $adminRole = $types[$request->type]['admin_role'] ?? 'hr';

            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->password),
                'role' => $adminRole,
                'company_id' => $company->id,
            ]);

            return [$company, $admin];
        });

        $admin->notify(new CompanyActivationNotification($company, $admin));

        return redirect()->route('register.cek-email', ['email' => $admin->email])
            ->with('success', 'Pendaftaran berhasil! Silakan cek email Anda untuk mengaktifkan akun.');
    }
}
