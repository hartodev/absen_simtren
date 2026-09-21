<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Tambah kolom `subdomain` di tabel companies.
     *
     * Subdomain inilah yang menjadi pintu masuk tiap tenant
     * (company / tpq-pesantren / sekolah), contoh:
     *   https://pt-maju.absen.milosgo.com
     *   https://tpq-alhidayah.absen.milosgo.com
     *   https://sman1.absen.milosgo.com
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('subdomain', 63)->nullable()->after('name');
        });

        // Isi subdomain untuk company yang sudah ada (backfill),
        // supaya tidak ada tenant lama yang "hilang" pintu masuknya.
        $companies = \DB::table('companies')->select('id', 'name')->get();

        foreach ($companies as $company) {
            $base = Str::slug($company->name);
            $base = $base !== '' ? $base : 'tenant-' . $company->id;
            $slug = $base;
            $i    = 1;

            while (\DB::table('companies')->where('subdomain', $slug)->where('id', '!=', $company->id)->exists()) {
                $slug = $base . '-' . (++$i);
            }

            \DB::table('companies')->where('id', $company->id)->update(['subdomain' => $slug]);
        }

        // Wajibkan NOT NULL + UNIQUE tanpa perlu paket doctrine/dbal
        // (yang dibutuhkan Blueprint::change() tapi belum tentu terpasang).
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            \DB::statement('CREATE UNIQUE INDEX companies_subdomain_unique ON companies (subdomain)');
        } else {
            \DB::statement('ALTER TABLE companies MODIFY subdomain VARCHAR(63) NOT NULL');
            Schema::table('companies', function (Blueprint $table) {
                $table->unique('subdomain');
            });
        }
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropUnique(['subdomain']);
            $table->dropColumn('subdomain');
        });
    }
};
