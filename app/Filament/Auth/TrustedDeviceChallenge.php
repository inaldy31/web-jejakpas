<?php

namespace App\Filament\Auth;

use App\Models\User;
use App\Support\TrustedDevices;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Auth\MultiFactor\MultiFactorChallenge;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Validation\ValidationException;

class TrustedDeviceChallenge extends MultiFactorChallenge
{
    public function getFirstEnabledProvider(Authenticatable $user): ?MultiFactorAuthenticationProvider
    {
        try {
            $provider = parent::getFirstEnabledProvider($user);
        } catch (DecryptException) {
            $this->refuseUnreadableTwoFactor($user);
        }

        if ($provider !== null && $user instanceof User && TrustedDevices::trusts($user, request())) {
            TrustedDevices::markUsed(request());

            return null;
        }

        return $provider;
    }

    private function refuseUnreadableTwoFactor(Authenticatable $user): never
    {
        if ($user instanceof User) {
            activity('auth')->performedOn($user)->event('two_factor_unreadable')
                ->withProperties(['username' => $user->username, 'ip' => request()->ip()])
                ->log('two_factor_unreadable');
        }

        throw ValidationException::withMessages([
            'data.username' => __('jejak.auth.two_factor_unreadable'),
        ]);
    }
}
