<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

final class TrustedDevices
{
    public const COOKIE = 'jejak_trusted_device';

    private const USED = 'jejak.trusted_device_used';

    public static function cookieFor(User $user): SymfonyCookie
    {
        $days = self::days();
        $expires = now()->addDays($days)->getTimestamp();

        return Cookie::make(
            self::COOKIE,
            $user->getKey().'|'.$expires.'|'.self::signature($user, $expires),
            $days * 24 * 60,
            '/',
            null,
            request()->isSecure() || (bool) config('session.secure'),
            true,
            false,
            'lax',
        );
    }

    public static function trusts(User $user, Request $request): bool
    {
        $value = $request->cookie(self::COOKIE);

        if (! is_string($value) || substr_count($value, '|') !== 2 || blank($user->trusted_device_key)) {
            return false;
        }

        [$id, $expires, $signature] = explode('|', $value);

        return $id === (string) $user->getKey()
            && ctype_digit($expires)
            && (int) $expires > now()->getTimestamp()
            && (int) $expires <= now()->addDays(self::days())->getTimestamp()
            && hash_equals(self::signature($user, (int) $expires), $signature);
    }

    public static function markUsed(Request $request): void
    {
        $request->attributes->set(self::USED, true);
    }

    public static function wasUsed(Request $request): bool
    {
        return $request->attributes->get(self::USED) === true;
    }

    private static function signature(User $user, int $expires): string
    {
        return hash_hmac('sha256', $user->getKey().'|'.$expires, $user->trusted_device_key);
    }

    private static function days(): int
    {
        return (int) config('jejak.security.trusted_device_days');
    }
}
