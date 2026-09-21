<?php

namespace App\Observers;

use App\Models\Kelas;
use App\Models\Tenant;

class TenantObserver
{
    public function created(Tenant $tenant): void
    {
        // Data awal supaya dashboard tenant baru tidak kosong melompong
        Kelas::create([
            'tenant_id' => $tenant->id,
            'nama_kelas' => 'Kelas 1',
        ]);

        // Di sini juga tempat yang tepat untuk:
        // - kirim notifikasi/email selamat datang ke admin tenant
        // - kirim notifikasi ke superadmin kalau status masih 'pending' (butuh approval)
    }
}