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
        Schema::create('prayers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('fajr')->nullable();
            $table->time('dzuhur')->nullable();
            $table->time('ashar')->nullable();
            $table->time('maghrib')->nullable();
            $table->time('isya')->nullable();
            $table->string('source')->default('aladhan_api');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayers');
    }
};
