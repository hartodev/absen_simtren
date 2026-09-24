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
            // Kolom 'department' belum pernah dibuat migration manapun,
            // padahal migration ini butuh kolom itu sudah ada (->after('department')).
            // Dibuat di sini supaya tidak error "Unknown column 'department'".
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable();
            }

            // Gaji pokok per karyawan — dipakai sebagai base_salary saat generate payroll otomatis
            if (!Schema::hasColumn('users', 'salary')) {
                $table->decimal('salary', 12, 2)->default(0)->after('department');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('users', 'salary') ? 'salary' : null,
                Schema::hasColumn('users', 'department') ? 'department' : null,
            ]));
        });
    }
};
