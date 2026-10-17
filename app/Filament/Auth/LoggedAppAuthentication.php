<?php

namespace App\Filament\Auth;

use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Illuminate\Database\Eloquent\Model;
use SensitiveParameter;

class LoggedAppAuthentication extends AppAuthentication
{
    public function saveSecret(HasAppAuthentication $user, #[SensitiveParameter] ?string $secret): void
    {
        parent::saveSecret($user, $secret);

        if ($user instanceof Model) {
            $event = filled($secret) ? 'two_factor_enabled' : 'two_factor_disabled';

            activity('admin_accounts')->causedBy($user)->performedOn($user)->event($event)->log($event);
        }
    }
}
