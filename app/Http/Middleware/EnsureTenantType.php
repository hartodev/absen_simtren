<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantType
{
    /**
     * Batasi route hanya untuk tenant dengan type tertentu.
     * Contoh pemakaian di routes: ->middleware('tenant.type:company')
     */
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        if (! app()->bound('tenant')) {
            abort(404);
        }

        $tenant = app('tenant');

        if (! in_array($tenant->type, $types, true)) {
            abort(403, 'Fitur ini tidak tersedia untuk tipe organisasi Anda.');
        }

        return $next($request);
    }
}
