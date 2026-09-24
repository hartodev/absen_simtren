<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Melengkapi 2026_09_18_100000_rename_tenant_id_to_company_id_on_users_table.php.
     *
     * Migration itu hanya mengganti nama kolom tenant_id -> company_id
     * dengan syarat "company_id BELUM ada". Padahal company_id sudah
     * dibuat lebih dulu oleh 2025_11_03_155515_add_companyid_to_users_table.php,
     * lalu tenant_id ditambahkan lagi sebagai kolom terpisah oleh
     * 2026_09_14_013411_add_tenant_id_to_users_tables.php. Akibatnya
     * migration rename tadi selalu no-op dan tabel users berakhir
     * dengan DUA kolom (company_id dan tenant_id) untuk konsep yang sama.
     *
     * Migration ini TIDAK mengubah migration lama (yang mungkin sudah
     * pernah jalan di production), melainkan menambahkan migration baru
     * yang mengambil field pentingnya saja: data pada tenant_id
     * dipindahkan ke company_id (untuk baris yang company_id-nya masih
     * kosong), lalu kolom tenant_id yang sudah redundan dihapus.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'tenant_id')) {
            return; // sudah bersih, tidak ada yang perlu dilakukan
        }

        // Pastikan company_id ada sebelum memindahkan data ke sana.
        if (!Schema::hasColumn('users', 'company_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()
                    ->constrained('companies')->nullOnDelete();
            });
        }

        // Ambil nilai penting dari tenant_id: isi company_id yang masih
        // kosong dengan nilai tenant_id-nya.
        DB::table('users')
            ->whereNull('company_id')
            ->whereNotNull('tenant_id')
            ->update(['company_id' => DB::raw('tenant_id')]);

        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['tenant_id']);
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'tenant_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('company_id');
        });

        DB::table('users')->update(['tenant_id' => DB::raw('company_id')]);
    }
};
