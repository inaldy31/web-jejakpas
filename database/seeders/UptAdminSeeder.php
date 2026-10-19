<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UptAdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('jejak.admin.password');

        if (blank($password)) {
            throw new RuntimeException('Set ADMIN_PASSWORD di .env sebelum menjalankan UptAdminSeeder.');
        }

        foreach (['lppbandung' => 'LPP Bandung', 'lppyogyakarta' => 'LPP Yogyakarta', 'lppbengkulu' => 'LPP Bengkulu'] as $username => $name) {
            User::firstOrCreate(
                ['username' => $username],
                ['name' => $name, 'email' => null, 'password' => Hash::make($password), 'role' => UserRole::Admin],
            );
        }
    }
}
