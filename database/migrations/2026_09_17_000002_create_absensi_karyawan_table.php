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
        Schema::create('absensi_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->string('lokasi_masuk')->nullable();   // format: "lat,lng"
            $table->string('lokasi_pulang')->nullable();  // format: "lat,lng"
            $table->enum('status', ['hadir', 'telat', 'izin', 'sakit', 'alpa', 'lembur'])->default('hadir');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // satu karyawan hanya punya satu baris absensi per tanggal
            $table->unique(['karyawan_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_karyawan');
    }
};
