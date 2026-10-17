<?php

namespace App\Support;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Hash;

final class AccountPasswords
{
    public static function notCurrentRule(User $user): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($user): void {
            if (is_string($value) && Hash::check($value, $user->getAuthPassword())) {
                $fail(__('jejak.account.password_same'));
            }
        };
    }

    public static function logChanged(User $user): void
    {
        activity('admin_accounts')->causedBy($user)->performedOn($user)->event('password_changed')->log('password_changed');
    }
}
