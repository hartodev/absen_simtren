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
        Schema::create('payrools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('allowance', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('overtime_pay', 12, 2)->default(0);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);
            $table->string('status')->default('draft'); // draft, approved, paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrools');
    }
};
