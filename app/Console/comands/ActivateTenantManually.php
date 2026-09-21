<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class ActivateTenantManually extends Command
{
    /**
     * php artisan tenant:activate {subdomain}
     * php artisan tenant:activate --all   (aktifkan semua tenant pending)
     */
    protected $signature = 'tenant:activate
                            {subdomain? : Subdomain tenant yang mau diaktifkan}
                            {--all : Aktifkan semua tenant berstatus pending}';

    protected $description = 'Aktivasi tenant secara manual (BYPASS email) — khusus untuk testing lokal';

    public function handle(): int
    {
        // Pagar keamanan: command ini hanya boleh jalan di local/testing,
        // supaya tidak ada yang tidak sengaja pakai di server production.
        if (! app()->environment(['local', 'testing'])) {
            $this->error('Perintah ini hanya diizinkan di environment local/testing. Environment saat ini: ' . app()->environment());
            $this->line('Di production, aktivasi tenant tetap wajib lewat email / approval superadmin.');
            return self::FAILURE;
        }

        if ($this->option('all')) {
            $tenants = Tenant::where('status', 'pending')->get();

            if ($tenants->isEmpty()) {
                $this->info('Tidak ada tenant berstatus pending.');
                return self::SUCCESS;
            }

            foreach ($tenants as $tenant) {
                $this->activate($tenant);
            }

            $this->info("Berhasil mengaktifkan {$tenants->count()} tenant.");
            return self::SUCCESS;
        }

        $subdomain = $this->argument('subdomain');

        if (! $subdomain) {
            $subdomain = $this->ask('Masukkan subdomain tenant yang mau diaktifkan');
        }

        $tenant = Tenant::where('subdomain', strtolower($subdomain))->first();

        if (! $tenant) {
            $this->error("Tenant dengan subdomain '{$subdomain}' tidak ditemukan.");
            return self::FAILURE;
        }

        if ($tenant->status === 'aktif') {
            $this->info("Tenant '{$tenant->nama_lembaga}' ({$tenant->subdomain}) sudah aktif.");
            return self::SUCCESS;
        }

        $this->activate($tenant);

        $mainDomain = config('app.main_domain');
        $this->info("Tenant '{$tenant->nama_lembaga}' berhasil diaktifkan.");
        $this->line("Silakan login di: http://{$tenant->subdomain}.{$mainDomain}:8000/login");

        return self::SUCCESS;
    }

    private function activate(Tenant $tenant): void
    {
        $tenant->update(['status' => 'aktif']);

        // Ikut verifikasi email semua user admin di tenant ini,
        // supaya tidak kejegal pengecekan email_verified_at di tempat lain.
        $tenant->users()
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }
}