<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mengubah subdomain di URL (mis. pt-maju.ptutamacta.com) menjadi
 * tenant (Company) yang aktif untuk request ini.
 *
 * Multi-lingkup: satu Company row = satu subdomain, dan `type`
 * (company / pesantren / school) menentukan lingkup apa itu.
 */
class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $subdomain = $request->route('tenant');

        if (!$subdomain) {
            abort(404, 'Tenant tidak ditemukan.');
        }

        $company = Company::bySubdomain($subdomain)->first();

        if (!$company) {
            return response()->view('pages.status.tidak-ditemukan', ['subdomain' => $subdomain], 404);
        }

        if ($company->status === 'pending') {
            return response()->view('pages.status.pending', ['company' => $company], 403);
        }

        if ($company->status === 'nonaktif') {
            return response()->view('pages.status.nonaktif', ['company' => $company], 403);
        }

        app()->instance('tenant', $company);
        $request->attributes->set('tenant', $company);

        // Supaya route('company.xxx') di mana pun (controller/blade) otomatis
        // terisi parameter {tenant} tanpa perlu ditulis manual tiap kali.
        URL::defaults(['tenant' => $subdomain]);

        return $next($request);
    }
}