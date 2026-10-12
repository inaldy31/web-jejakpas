<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum UptType: string implements HasLabel
{
    use HasOptions;

    case Lapas = 'lapas';
    case Lpp = 'lpp';
    case Rutan = 'rutan';
    case Bapas = 'bapas';
    case Lainnya = 'lainnya';

    public function getLabel(): string
    {
        return match ($this) {
            self::Lapas => 'Lapas',
            self::Lpp => 'LPP (Lapas Perempuan)',
            self::Rutan => 'Rutan',
            self::Bapas => 'Bapas',
            self::Lainnya => 'Lainnya',
        };
    }
}
