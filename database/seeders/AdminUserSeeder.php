<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $username = Str::lower(trim((string) config('jejak.admin.username')));

        if (blank($username)) {
            throw new RuntimeException('Set ADMIN_USERNAME di .env sebelum menjalankan AdminUserSeeder.');
        }

        $password = config('jejak.admin.password');

        if (blank($password)) {
            throw new RuntimeException('Set ADMIN_PASSWORD di .env sebelum menjalankan AdminUserSeeder.');
        }

        $rules = Validator::make(['password' => $password], ['password' => Password::defaults()], [], ['password' => 'ADMIN_PASSWORD']);

        if ($rules->fails()) {
            throw new RuntimeException('ADMIN_PASSWORD di .env tidak memenuhi aturan kata sandi Admin: '.$rules->errors()->first('password').' Tidak ada yang diubah.');
        }

        DB::transaction(function () use ($username, $password): void {
            $admin = User::updateOrCreate(
                ['username' => $username],
                ['name' => filled(config('jejak.admin.name')) ? config('jejak.admin.name') : 'Admin JEJAK PAS', 'email' => null, 'password' => Hash::make($password), 'role' => UserRole::Admin],
            );

            if (! User::where('is_primary_admin', true)->whereNull('deactivated_at')->whereKeyNot($admin->getKey())->exists()) {
                User::where('is_primary_admin', true)->whereKeyNot($admin->getKey())->update(['is_primary_admin' => false]);
                $admin->forceFill(['is_primary_admin' => true, 'upt_id' => null])->save();
            }
        });

        $this->command?->info(__('jejak.admins.console.primary_now', [
            'username' => User::where('is_primary_admin', true)->whereNull('deactivated_at')->value('username'),
        ]));
    }
}
