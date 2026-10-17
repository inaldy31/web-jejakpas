<?php

namespace App\Support;

use App\Models\User;

final class AdminAccounts
{
    private const ALPHABET = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public static function temporaryPassword(): string
    {
        $groups = [];

        for ($group = 0; $group < 4; $group++) {
            $chars = '';

            for ($i = 0; $i < 4; $i++) {
                $chars .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
            }

            $groups[] = $chars;
        }

        return implode('-', $groups);
    }

    public static function resetPassword(User $user): string
    {
        $password = self::temporaryPassword();

        $user->password = $password;
        $user->must_change_password = true;
        $user->save();

        return $password;
    }

    public static function resetTwoFactor(User $user): void
    {
        $user->app_authentication_secret = null;
        $user->app_authentication_recovery_codes = null;
        $user->save();
    }

    public static function log(string $event, User $subject, ?User $causer, array $properties = []): void
    {
        $activity = activity('admin_accounts')->performedOn($subject)->event($event)->withProperties($properties);

        if ($causer !== null) {
            $activity->causedBy($causer);
        } else {
            $activity->causedByAnonymous();
        }

        $activity->log($event);
    }
}
