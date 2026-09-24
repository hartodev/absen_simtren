<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel 'companies' dibuat dengan kolom email, address, latitude,
     * longitude, radius_km, time_in, time_out sebagai NOT NULL tanpa
     * default. Tapi alur pendaftaran perusahaan sekarang
     * (RegisterController@store) hanya mengisi name, subdomain, type,
     * status saat company dibuat — profil lengkap (alamat, lokasi,
     * jam kerja, dst) diisi belakangan lewat halaman setting company.
     * Ini menyebabkan error "Field 'email' doesn't have a default
     * value" (dan akan berlanjut ke kolom berikutnya kalau dibiarkan).
     *
     * Sama seperti migration lain di project ini (lihat
     * change_bank_column_to_varchar, rename_tenant_id_to_company_id),
     * dipakai raw SQL supaya tidak butuh paket doctrine/dbal.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite tidak strict soal NOT NULL tanpa default kalau
            // aplikasi tidak mengisinya lewat query builder biasa,
            // jadi tidak perlu ALTER khusus di sini.
            return;
        }

        DB::statement("ALTER TABLE companies MODIFY email VARCHAR(255) NULL");
        DB::statement("ALTER TABLE companies MODIFY address VARCHAR(255) NULL");
        DB::statement("ALTER TABLE companies MODIFY latitude VARCHAR(255) NULL");
        DB::statement("ALTER TABLE companies MODIFY longitude VARCHAR(255) NULL");
        DB::statement("ALTER TABLE companies MODIFY radius_km VARCHAR(255) NOT NULL DEFAULT '0.5'");
        DB::statement("ALTER TABLE companies MODIFY time_in VARCHAR(255) NOT NULL DEFAULT '08:00'");
        DB::statement("ALTER TABLE companies MODIFY time_out VARCHAR(255) NOT NULL DEFAULT '17:00'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE companies MODIFY email VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE companies MODIFY address VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE companies MODIFY latitude VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE companies MODIFY longitude VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE companies MODIFY radius_km VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE companies MODIFY time_in VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE companies MODIFY time_out VARCHAR(255) NOT NULL");
    }
};
