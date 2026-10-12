<?php

namespace App\Enums\Concerns;

trait HasOptions
{
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }

        return $options;
    }

    public static function fromLabel(?string $label): ?self
    {
        $needle = self::normalizeLabel($label);

        if ($needle === '') {
            return null;
        }

        foreach (self::cases() as $case) {
            if (self::normalizeLabel($case->getLabel()) === $needle || self::normalizeLabel($case->value) === $needle) {
                return $case;
            }
        }

        return null;
    }

    private static function normalizeLabel(?string $value): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim((string) $value)));
    }
}
