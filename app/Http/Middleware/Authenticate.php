<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Tujuan redirect saat user belum login.
     * Nama route harus sama dengan routes/web.php:
     *   - subdomain tenant -> 'tenant.login.form' (butuh parameter {tenant})
     *   - domain utama     -> 'superadmin.login.form' (login superadmin;
     *     'login.form' sekarang gerbang cari-subdomain, bukan form login)
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        if ($subdomain = $request->route('tenant')) {
            return route('tenant.login.form', ['tenant' => $subdomain]);
        }

        return route('superadmin.login.form');
    }
}       