<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Notifications\CompanyActivationNotification;
use Illuminate\Http\Request;

class ActivationController extends Controller
{
    public function cekEmail(Request $request)
    {
        return view('pages.auth.cek-email', ['email' => $request->query('email')]);
    }

    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->company && $user->company->status === 'pending') {
            $user->notify(new CompanyActivationNotification($user->company, $user));
        }

        return back()->with('success', 'Jika akun ditemukan dan masih menunggu aktivasi, email aktivasi baru telah dikirim.');
    }

    public function activate(Request $request, Company $company, User $user)
    {
        if ((int) $user->company_id !== (int) $company->id) {
            abort(403);
        }

        if ($company->status === 'pending') {
            $company->update(['status' => 'aktif']);
        }

        if (is_null($user->email_verified_at)) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        // Alur: Daftar -> email aktivasi -> halaman "isi subdomain" -> login subdomain.
        // Setelah aktivasi, arahkan ke halaman isi-subdomain (subdomain sudah terisi);
        // route() otomatis ikut scheme & port yang sedang dipakai (mis. :8000).
        return redirect()
            ->route('login.form', ['subdomain' => $company->subdomain])
            ->with('success', 'Akun berhasil diaktivasi! Klik "Lanjut ke login" untuk masuk ke halaman login lembaga Anda.');
    }
}
