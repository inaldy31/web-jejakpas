<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum SustainabilityStatus: string implements HasLabel
{
    use HasOptions;

    case Berlanjut = 'berlanjut';
    case Berisiko = 'berisiko';
    case Terhenti = 'terhenti';

    public function getLabel(): string
    {
        return match ($this) {
            self::Berlanjut => 'Berlanjut',
            self::Berisiko => 'Berisiko',
            self::Terhenti => 'Terhenti',
        };
    }
}
