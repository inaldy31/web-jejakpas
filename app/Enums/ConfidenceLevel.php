<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum ConfidenceLevel: string implements HasLabel
{
    use HasOptions;

    case SangatSiap = 'sangat_siap';
    case Siap = 'siap';
    case CukupSiap = 'cukup_siap';
    case BelumSiap = 'belum_siap';

    public static function fromTotal(int $total): self
    {
        return match (true) {
            $total >= 81 => self::SangatSiap,
            $total >= 61 => self::Siap,
            $total >= 41 => self::CukupSiap,
            default => self::BelumSiap,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::SangatSiap => 'Sangat Siap',
            self::Siap => 'Siap',
            self::CukupSiap => 'Cukup Siap/Perlu Penguatan',
            self::BelumSiap => 'Belum Siap',
        };
    }
}
