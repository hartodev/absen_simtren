<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menggantikan migration duplikat
     * "2026_09_18_200002_create_permissions_table.php" yang mencoba
     * membuat ulang tabel 'permissions' padahal tabel ini sudah dibuat
     * sejak 2025_04_20_221819_create_permissions_table.php (dan sudah
     * mendapat kolom company_id lewat 2026_02_15_051335, serta
     * is_approved dibuat nullable lewat 2026_02_16_050024).
     *
     * Schema::create() pada tabel yang sudah ada akan error, jadi
     * migration duplikat itu dihapus. Namun di dalamnya ada 3 kolom
     * yang memang belum pernah ditambahkan ke tabel ini:
     *   - type          (jenis permission: izin | sakit | cuti, dst)
     *   - approved_by   (siapa yang menyetujui)
     *   - approved_at   (kapan disetujui)
     * Field-field penting inilah yang diambil dan ditambahkan di sini,
     * bukan me-recreate seluruh tabel.
     */
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('permissions', 'type')) {
                $table->string('type')->default('izin')->after('date_permission'); // izin | sakit | cuti
            }

            if (!Schema::hasColumn('permissions', 'approved_by')) {
                $table->foreignId('approved_by')
                    ->nullable()
                    ->after('is_approved')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('permissions', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            if (Schema::hasColumn('permissions', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            $table->dropColumn(array_filter([
                Schema::hasColumn('permissions', 'approved_at') ? 'approved_at' : null,
                Schema::hasColumn('permissions', 'type') ? 'type' : null,
            ]));
        });
    }
};
