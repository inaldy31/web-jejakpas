<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum CompetencyField: string implements HasLabel
{
    use HasOptions;

    case Kuliner = 'kuliner';
    case Kriya = 'kriya';
    case Fesyen = 'fesyen';
    case Kecantikan = 'kecantikan';
    case KeterampilanDigital = 'keterampilan_digital';
    case Lainnya = 'lainnya';

    public function getLabel(): string
    {
        return match ($this) {
            self::Kuliner => 'Kuliner',
            self::Kriya => 'Kriya',
            self::Fesyen => 'Fesyen',
            self::Kecantikan => 'Kecantikan',
            self::KeterampilanDigital => 'Keterampilan Digital',
            self::Lainnya => 'Lainnya',
        };
    }
}
