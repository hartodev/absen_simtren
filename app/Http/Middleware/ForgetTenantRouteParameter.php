<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route subdomain tenant memakai domain '{tenant}.domain', jadi Laravel ikut
 * meneruskan {tenant} sebagai argumen pertama ke method controller.
 * Middleware ini membuangnya (setelah ResolveTenant & Authenticate selesai
 * membacanya) supaya method seperti edit(int $id) tetap menerima id yang benar.
 * URL::defaults() dari ResolveTenant tetap membuat route() terisi otomatis.
 */
class ForgetTenantRouteParameter
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->route()?->forgetParameter('tenant');

        return $next($request);
    }
}
