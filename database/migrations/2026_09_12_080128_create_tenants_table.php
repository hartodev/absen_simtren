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
      Schema::create('tenants', function (Blueprint $table) {
    $table->id();
    $table->string('nama_lembaga');
    $table->string('subdomain')->unique();
    $table->string('logo')->nullable();
    $table->string('warna_tema')->default('#2563eb');
    $table->string('paket')->default('dasar');
    $table->enum('status', ['pending', 'aktif', 'nonaktif'])->default('pending');
    $table->boolean('publik')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};