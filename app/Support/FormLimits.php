<?php

namespace App\Support;

use Filament\Forms\Components\Textarea;

final class FormLimits
{
    public const TEXTAREA = 5000;

    public static function limit(Textarea $field): void
    {
        $field->maxLength(self::TEXTAREA)->helperText(self::hint());
    }

    public static function hint(?string $own = null): string
    {
        return trim($own.' '.__('jejak.form.max_length', ['max' => Format::number(self::TEXTAREA)]));
    }
}
