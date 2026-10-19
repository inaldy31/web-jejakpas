<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Upt;
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

        $accounts = [
            'lppbandung' => ['LPP Bandung', UptSeeder::BANDUNG],
            'lppyogyakarta' => ['LPP Yogyakarta', UptSeeder::YOGYAKARTA],
            'lppbengkulu' => ['LPP Bengkulu', UptSeeder::BENGKULU],
        ];

        $upts = [];

        foreach ($accounts as [, $uptName]) {
            $upts[$uptName] = Upt::firstWhere('name', $uptName)
                ?? throw new RuntimeException("UPT \"{$uptName}\" belum ada. Jalankan UptSeeder sebelum UptAdminSeeder. Tidak ada akun yang dibuat.");
        }

        foreach ($accounts as $username => [$name, $uptName]) {
            $user = User::firstOrCreate(
                ['username' => $username],
                ['name' => $name, 'email' => null, 'password' => Hash::make($password), 'role' => UserRole::Admin],
            );

            $upt = $upts[$uptName];

            if ($user->upt_id === null && ! $user->is_primary_admin) {
                $user->forceFill(['upt_id' => $upt->getKey()])->save();
            }
        }
    }
}
