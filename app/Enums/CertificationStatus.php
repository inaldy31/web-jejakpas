<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum CertificationStatus: string implements HasLabel
{
    use HasOptions;

    case Bersertifikat = 'bersertifikat';
    case TidakBersertifikat = 'tidak_bersertifikat';
    case BelumTersedia = 'belum_tersedia';

    public static function fromCount(?int $certified): self
    {
        return match (true) {
            $certified === null => self::BelumTersedia,
            $certified > 0 => self::Bersertifikat,
            default => self::TidakBersertifikat,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Bersertifikat => 'Bersertifikat',
            self::TidakBersertifikat => 'Tidak Bersertifikat',
            self::BelumTersedia => 'Belum tersedia di sumber',
        };
    }
}
