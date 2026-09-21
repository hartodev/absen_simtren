<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Simpan file ini di: database/migrations/
// Nama file harus PERSIS seperti ini (tanggal di depan penting untuk urutan migration)
// Jalankan setelah disimpan: php artisan migrate

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (! Schema::hasColumn('tenants', 'tipe_lembaga')) {
                // Nilainya harus salah satu dari key di config/tenant_types.php
                // contoh: 'company', 'tpq', 'sekolah'
                $table->string('tipe_lembaga')->default('company')->after('nama_lembaga');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('tipe_lembaga');
        });
    }
};