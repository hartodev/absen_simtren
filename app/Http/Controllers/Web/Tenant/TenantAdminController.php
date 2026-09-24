<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

/**
 * Basis controller admin lembaga (pesantren / school) untuk halaman web admin.
 * Route-nya dibungkus middleware auth + tenant.type + role (lihat routes/web.php),
 * jadi di sini cukup helper: tenant aktif, tema, dan render view.
 */
abstract class TenantAdminController extends Controller
{
    protected function company(): Company
    {
        if (app()->bound('tenant')) {
            return app('tenant');
        }

        abort_unless(Auth::user()?->company, 422, 'Tenant tidak ditemukan.');

        return Auth::user()->company;
    }

    /** Semua query WAJIB lewat sini supaya data antar-tenant tidak bocor. */
    protected function cid(): int
    {
        return $this->company()->id;
    }

    /**
     * Render view dengan nama pendek, mis. view('students.index').
     * Dicari berurutan (yang pertama ketemu dipakai):
     *   1. pages/{school|pesantren}/...  -> khusus sekolah / pesantren (override)
     *   2. pages/lembaga/...             -> dipakai bersama sekolah & pesantren
     */
    protected function view(string $name, array $data = [])
    {
        $company = $this->company();

        return view()->first([
            "pages.{$company->type}.{$name}",
            "pages.lembaga.{$name}",
        ], $data + [
            'company' => $company,
            'theme'   => $this->isTpq($company) ? 'green' : 'blue',
            'rp'      => $company->type . '.',   // prefix nama route: school. / pesantren.
        ]);
    }

    /** Tampilan hijau (TPQ) hanya untuk pesantren; sekolah selalu biru. */
    protected function isTpq(Company $company): bool
    {
        return $company->type === 'pesantren'
            && $company->dashboardStyle() === Company::DASHBOARD_SIMPLE;
    }

    protected function to(string $route, array $params = [])
    {
        return redirect()->route($this->company()->type . '.' . $route, $params);
    }
}
