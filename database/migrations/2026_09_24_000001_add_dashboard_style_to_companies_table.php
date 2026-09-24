<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'dashboard_style')) {
                // null  = otomatis (lihat Company::dashboardStyle())
                // simple = tampilan hijau (TPQ)
                // full   = tampilan biru grid modul (sekolah umum / pondok pesantren)
                $table->string('dashboard_style', 10)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'dashboard_style')) {
                $table->dropColumn('dashboard_style');
            }
        });
    }
};
