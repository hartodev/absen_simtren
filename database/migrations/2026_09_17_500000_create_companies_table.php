<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel dasar companies -- sebelumnya cuma ada migrasi ALTER
     * (add_subdomain, add_status) yang mengasumsikan tabel ini sudah ada,
     * padahal belum pernah dibuat. Migrasi ini WAJIB berjalan SEBELUM
     * kedua migrasi ALTER tersebut (perhatikan timestamp nama file).
     */
    public function up(): void
    {
        if (Schema::hasTable('companies')) {
            return;
        }

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('company'); // company | pesantren | school
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('radius_km', 6, 2)->default(0.5);
            $table->time('time_in')->default('08:00:00');
            $table->time('time_out')->default('17:00:00');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
