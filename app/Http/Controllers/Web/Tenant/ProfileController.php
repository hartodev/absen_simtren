<?php

namespace App\Http\Controllers\Web\Tenant;

use Illuminate\Support\Facades\Auth;

/** Tab "Profil" (biru) / "Lainnya" (hijau). */
class ProfileController extends TenantAdminController
{
    public function show()
    {
        return $this->view('profile', ['user' => Auth::user()]);
    }
}
