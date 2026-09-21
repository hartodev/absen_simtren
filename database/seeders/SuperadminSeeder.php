<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    /**
     * Jalankan dengan: php artisan db:seed --class=SuperadminSeeder
     * Ganti email & password di bawah sebelum dipakai di production.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@absen.id'],
            [
                'name' => 'Superadmin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'tenant_id' => null,
            ]
        );
    }
}