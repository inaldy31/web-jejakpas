<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\AdminAccounts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssignPrimaryAdmin extends Command
{
    protected $signature = 'jejak:admin-utama {username : Nama pengguna Admin yang ditunjuk}';

    protected $description = 'Tunjuk Admin Utama baru (satu-satunya yang bisa membuka Kelola Admin); Admin Utama lama menjadi Admin biasa.';

    public function handle(): int
    {
        $username = Str::lower(trim((string) $this->argument('username')));
        $user = User::where('username', $username)->first();

        if ($user === null) {
            $this->error(__('jejak.admins.console.not_found', ['username' => $username]));

            return self::FAILURE;
        }

        if (! $user->isActive()) {
            $this->error(__('jejak.admins.console.deactivated', ['username' => $user->username]));

            return self::FAILURE;
        }

        if ($user->upt_id !== null) {
            $this->error(__('jejak.admins.console.has_upt', ['username' => $user->username]));

            return self::FAILURE;
        }

        if ($user->is_primary_admin && ! User::where('is_primary_admin', true)->whereKeyNot($user->getKey())->exists()) {
            $this->info(__('jejak.admins.console.primary_unchanged', ['username' => $user->username]));

            return self::SUCCESS;
        }

        $previous = DB::transaction(function () use ($user): array {
            $previous = User::where('is_primary_admin', true)->whereKeyNot($user->getKey())->pluck('username')->all();

            User::where('is_primary_admin', true)->whereKeyNot($user->getKey())->update(['is_primary_admin' => false]);
            $user->forceFill(['is_primary_admin' => true])->save();

            return $previous;
        });

        AdminAccounts::log('primary_admin_assigned', $user, null, ['via' => __('jejak.admins.console.via'), 'previous' => $previous]);

        $this->info($previous === []
            ? __('jejak.admins.console.primary_assigned_without_previous', ['username' => $user->username])
            : __('jejak.admins.console.primary_assigned', ['username' => $user->username]));

        return self::SUCCESS;
    }
}
