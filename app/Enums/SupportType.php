<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum SupportType: string implements HasLabel
{
    use HasOptions;

    case InKind = 'in_kind';
    case Pelatihan = 'pelatihan';
    case Sertifikasi = 'sertifikasi';
    case Pemasaran = 'pemasaran';

    public function getLabel(): string
    {
        return match ($this) {
            self::InKind => 'In-kind',
            self::Pelatihan => 'Pelatihan',
            self::Sertifikasi => 'Sertifikasi',
            self::Pemasaran => 'Pemasaran',
        };
    }
}
