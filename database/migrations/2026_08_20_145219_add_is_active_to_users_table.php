<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_active')) {
                // Catatan: sebelumnya pakai ->after('role'), tapi kolom 'role'
                // baru dibuat migration 2026_09_16_000000 (lebih belakangan),
                // jadi ->after('role') akan error di fresh install. Posisi
                // kolom dihilangkan karena tidak krusial secara fungsional.
                $table->boolean('is_active')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
