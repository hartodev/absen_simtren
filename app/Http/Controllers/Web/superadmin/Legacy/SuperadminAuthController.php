<?php

namespace App\Http\Controllers\Web\SuperAdmin\Legacy;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * LEGACY -- tidak terdaftar di routes/web.php (sisa desain lama). Aman dihapus
 * kalau sudah pasti tidak dipakai; view-nya ada di resources/views/_legacy.
 */
class SuperadminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isSuperadmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        return view('_legacy.superadmin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])
            ->where('role', 'superadmin')
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi salah, atau akun ini bukan superadmin.',
            ])->onlyInput('email');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('superadmin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login');
    }
}
