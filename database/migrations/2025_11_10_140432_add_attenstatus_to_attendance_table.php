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
        Schema::table('attendances', function (Blueprint $table) {
                        $table->enum('status', ['on_time', 'late', 'absent', 'permission', 'overtime', 'guest'])->default('on_time');
            $table->integer('overtime_minutes')->default(0);
            $table->boolean('approved_overtime')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            //
        });
    }
};
