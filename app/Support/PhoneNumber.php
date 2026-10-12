<?php

namespace App\Support;

final class PhoneNumber
{
    private const MIN_DIGITS = 7;

    private const GROUP = '(?:\(\+?[0-9]+\)|[0-9]++)';

    private const NUMBER = '/\+?'.self::GROUP.'(?:[\s.-]{0,2}+'.self::GROUP.')*+/u';

    public static function firstIn(string $text): ?array
    {
        if (! preg_match_all(self::NUMBER, $text, $found, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        foreach ($found[0] as [$number, $offset]) {
            $digits = preg_replace('/[^0-9]/', '', $number);

            if (strlen($digits) >= self::MIN_DIGITS) {
                return [
                    'before' => substr($text, 0, $offset),
                    'number' => $number,
                    'after' => substr($text, $offset + strlen($number)),
                    'dial' => (str_starts_with(ltrim($number, '('), '+') ? '+' : '').$digits,
                ];
            }
        }

        return null;
    }
}
