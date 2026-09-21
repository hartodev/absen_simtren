<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seluruh kode "versi baru" (Company/context-based) mengakses
     * $user->company_id, tapi kolom fisik di database masih tenant_id
     * (peninggalan skema "versi lama"/Tenant). Migrasi ini menyamakannya.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'tenant_id') && !Schema::hasColumn('users', 'company_id')) {
            $driver = Schema::getConnection()->getDriverName();

            if ($driver !== 'sqlite') {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['tenant_id']);
                });
            }

            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('tenant_id', 'company_id');
            });

            if ($driver !== 'sqlite') {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
                });
            }
        }

        // Default role lama 'tenant' tidak dipakai lagi di skema baru
        // (role sekarang: superadmin, hr, employee, ustadz, santri, teacher, student)
        // Pakai raw SQL supaya tidak butuh paket doctrine/dbal (untuk ->change()).
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'sqlite') {
            \DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'employee'");
        }
        // SQLite: default lama tetap ada tapi tidak masalah, karena semua insert
        // di kode aplikasi selalu mengisi role secara eksplisit.

        DB::table('users')->where('role', 'tenant')->update(['role' => 'employee']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'company_id') && !Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
            });

            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('company_id', 'tenant_id');
            });
        }
    }
};
