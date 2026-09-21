<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TenantAuthController extends Controller
{
    public function showLogin()
    {
        $tenant = app('currentTenant');
        return view('tenant.login', compact('tenant'));
    }

    public function login(Request $request)
    {
        $tenant = app('currentTenant');

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Wajib difilter tenant_id, supaya akun tenant lain tidak bisa dipakai login di sini
        $user = \App\Models\User::where('email', $credentials['email'])
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi salah, atau akun ini bukan milik lembaga ini.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('tenant.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tenant.login');
    }
}