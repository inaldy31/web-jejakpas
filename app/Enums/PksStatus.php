<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PksStatus: string implements HasColor, HasLabel
{
    use HasOptions;

    case Aktif = 'aktif';
    case Berakhir = 'berakhir';
    case DalamProses = 'dalam_proses';

    public function getLabel(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Berakhir => 'Berakhir',
            self::DalamProses => 'Dalam Proses',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Aktif => 'success',
            self::DalamProses => 'warning',
            self::Berakhir => 'danger',
        };
    }
}
