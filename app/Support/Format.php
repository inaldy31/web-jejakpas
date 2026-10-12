<?php

namespace App\Support;

final class Format
{
    public static function number(int|float|null $value, int $decimals = 0): string
    {
        if ($value === null) {
            return __('jejak.not_available');
        }

        return number_format((float) $value, $decimals, ',', '.');
    }

    public static function share(int $count, int $total): string
    {
        return $total > 0 ? self::number($count / $total * 100).'%' : '—';
    }
}
