<?php

namespace App\Http\Controllers\Web;

use App\Models\Company;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    /**
     * Halaman "cari lembaga saya" di domain utama.
     * Ini BUKAN halaman login sungguhan -- cuma gerbang untuk
     * mengarahkan user ke subdomain lembaganya masing-masing.
     */
    public function show(Request $request)
    {
        // ?subdomain=xxx dipakai setelah aktivasi akun supaya kolom langsung terisi.
        return view('pages.auth.portal', [
            'subdomain' => $request->query('subdomain'),
        ]);
    }

    public function redirectToTenant(Request $request)
    {
        $request->validate([
            'subdomain' => 'required|alpha_dash',
        ]);

        // Company (tabel companies) adalah sumber kebenaran tenant sekarang,
        // sama dengan yang dipakai RegisterController & middleware ResolveTenant.
        // (Model Tenant / tabel tenants adalah versi lama.)
        $company = Company::bySubdomain(strtolower($request->subdomain))->first();

        if (! $company) {
            return back()
                ->withInput()
                ->withErrors(['subdomain' => 'Subdomain lembaga tidak ditemukan.']);
        }

        if ($company->status === 'pending') {
            return back()
                ->withInput()
                ->withErrors(['subdomain' => 'Lembaga ini belum diaktivasi. Silakan cek email aktivasi yang dikirim saat pendaftaran.']);
        }

        if ($company->status === 'nonaktif') {
            return back()
                ->withInput()
                ->withErrors(['subdomain' => 'Lembaga ini sedang tidak aktif. Hubungi admin untuk informasi lebih lanjut.']);
        }

        $scheme = $request->getScheme();
        $port   = $request->getPort();
        $port   = in_array($port, [80, 443], true) ? '' : ':' . $port;

        return redirect("{$scheme}://{$company->subdomain}." . config('app.tenant_domain') . "{$port}/login");
    }
}
