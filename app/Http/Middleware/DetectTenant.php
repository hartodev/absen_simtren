<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class DetectTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $mainDomain = config('app.main_domain');

        $subdomain = str_replace('.' . $mainDomain, '', $host);

        if ($subdomain === $host) {
            // tidak ada subdomain, akses domain utama
            return $next($request);
        }

        $tenant = \App\Models\Tenant::where('subdomain', $subdomain)->first();

        if (!$tenant) {
            return response()->view('tenant.tidak-ditemukan', ['subdomain' => $subdomain], 404);
        }

        if ($tenant->status === 'pending') {
            return response()->view('tenant.pending', ['tenant' => $tenant], 403);
        }

        if ($tenant->status === 'nonaktif') {
            return response()->view('tenant.nonaktif', ['tenant' => $tenant], 403);
        }

        app()->instance('currentTenant', $tenant);

        // Supaya route('tenant.login.submit'), route('tenant.dashboard'), dll
        // otomatis terisi parameter {subdomain}-nya tanpa perlu ditulis manual tiap kali.
        URL::defaults(['subdomain' => $subdomain]);

        return $next($request);
    }
}
