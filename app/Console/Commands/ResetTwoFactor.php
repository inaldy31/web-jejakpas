<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\AdminAccounts;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ResetTwoFactor extends Command
{
    protected $signature = 'jejak:reset-2fa {username : Nama pengguna Admin}';

    protected $description = 'Reset 2FA seorang Admin (secret, kode pemulihan, perangkat yang diingat); Admin itu wajib memasang 2FA lagi saat masuk.';

    public function handle(): int
    {
        $username = Str::lower(trim((string) $this->argument('username')));
        $user = User::where('username', $username)->first();

        if ($user === null) {
            $this->error(__('jejak.admins.console.not_found', ['username' => $username]));

            return self::FAILURE;
        }

        if (blank($user->getRawOriginal('app_authentication_secret')) && blank($user->getRawOriginal('app_authentication_recovery_codes'))) {
            $this->info(__('jejak.admins.console.two_factor_unchanged', ['username' => $user->username]));

            return self::SUCCESS;
        }

        AdminAccounts::resetTwoFactor($user);
        AdminAccounts::log('two_factor_reset', $user, null, ['via' => __('jejak.admins.console.via')]);

        $this->info(__('jejak.admins.console.two_factor_reset', ['username' => $user->username]));

        return self::SUCCESS;
    }
}
