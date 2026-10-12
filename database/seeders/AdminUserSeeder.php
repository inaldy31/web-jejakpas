<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $username = config('jejak.admin.username');

        if (blank($username)) {
            throw new RuntimeException('Set ADMIN_USERNAME di .env sebelum menjalankan AdminUserSeeder.');
        }

        $password = config('jejak.admin.password');

        if (blank($password)) {
            throw new RuntimeException('Set ADMIN_PASSWORD di .env sebelum menjalankan AdminUserSeeder.');
        }

        User::updateOrCreate(
            ['username' => $username],
            ['name' => 'Admin JEJAK PAS', 'email' => null, 'password' => Hash::make($password), 'role' => UserRole::Admin],
        );
    }
}
