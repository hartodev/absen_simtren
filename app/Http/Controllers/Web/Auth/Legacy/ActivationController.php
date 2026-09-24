<?php

namespace App\Http\Controllers\Web\Auth\Legacy;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\TenantActivationNotification;
use Illuminate\Http\Request;

/**
 * LEGACY -- tidak terdaftar di routes/web.php (sisa desain lama). Aman dihapus
 * kalau sudah pasti tidak dipakai; view-nya ada di resources/views/_legacy.
 */
class ActivationController extends Controller
{
    /**
     * Halaman "cek email" setelah daftar.
     */
    public function cekEmail(Request $request)
    {
        return view('_legacy.cek-email', ['email' => $request->query('email')]);
    }

    /**
     * Kirim ulang email aktivasi.
     */
    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->tenant && $user->tenant->status === 'pending') {
            $user->notify(new TenantActivationNotification($user->tenant, $user));
        }

        // Pesan sama baik email ada atau tidak, supaya tidak bocor info akun mana yang terdaftar
        return back()->with('success', 'Jika akun ditemukan dan masih menunggu aktivasi, email aktivasi baru telah dikirim.');
    }

    /**
     * Diproses saat user klik link di email (signed URL, otomatis divalidasi oleh middleware 'signed').
     */
    public function activate(Request $request, Tenant $tenant, User $user)
    {
        if ($user->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($tenant->status === 'pending') {
            $tenant->update(['status' => 'aktif']);
        }

        if (is_null($user->email_verified_at)) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $mainDomain = config('app.main_domain');
        $scheme = $request->getScheme();
        $port = $request->getPort();
        $port = in_array($port, [80, 443]) ? '' : ':' . $port;

        return redirect("{$scheme}://{$tenant->subdomain}.{$mainDomain}{$port}/login")
            ->with('success', 'Akun berhasil diaktivasi! Silakan login.');
    }
}