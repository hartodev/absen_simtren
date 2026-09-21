<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Simpan file ini di: database/migrations/
// Lalu jalankan: php artisan migrate
//
// Kalau kolom 'warna_tema' sudah ada sebelumnya (sudah kepakai di kode kamu),
// migration ini hanya menambah 'logo' dan 'deskripsi' — aman untuk dijalankan
// di project yang sudah berjalan.

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (! Schema::hasColumn('tenants', 'logo')) {
                // path relatif ke storage, contoh: 'tenant-logos/tpq.png'
                $table->string('logo')->nullable()->after('nama_lembaga');
            }

            if (! Schema::hasColumn('tenants', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('logo');
            }

            if (! Schema::hasColumn('tenants', 'warna_tema')) {
                // fallback kalau ternyata kolom ini belum ada
                $table->string('warna_tema')->default('#102b69')->after('deskripsi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['logo', 'deskripsi']);
        });
    }
};  