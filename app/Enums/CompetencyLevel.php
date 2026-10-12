<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum CompetencyLevel: string implements HasLabel
{
    use HasOptions;

    case SangatKompeten = 'sangat_kompeten';
    case Kompeten = 'kompeten';
    case CukupKompeten = 'cukup_kompeten';
    case MemerlukanPenguatan = 'memerlukan_penguatan';

    public static function fromTotal(int $total): self
    {
        return match (true) {
            $total >= 49 => self::SangatKompeten,
            $total >= 37 => self::Kompeten,
            $total >= 25 => self::CukupKompeten,
            default => self::MemerlukanPenguatan,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::SangatKompeten => 'Sangat Kompeten',
            self::Kompeten => 'Kompeten',
            self::CukupKompeten => 'Cukup Kompeten',
            self::MemerlukanPenguatan => 'Memerlukan Penguatan',
        };
    }
}
