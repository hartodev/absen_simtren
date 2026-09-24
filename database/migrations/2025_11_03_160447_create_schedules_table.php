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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_datetime');
            $table->json('reminder_offsets')->nullable(); // contoh: [5, 15, 60] (menit)
            $table->boolean('is_task_duty')->default(false);
            $table->json('location')->nullable();
            $table->string('status')->default('upcoming'); // upcoming, done, canceled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
