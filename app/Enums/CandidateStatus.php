<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CandidateStatus: string implements HasColor, HasLabel
{
    use HasOptions;

    case Calon = 'calon';
    case Siap = 'siap';
    case SiapDenganPenguatan = 'siap_dengan_penguatan';
    case BelumSiap = 'belum_siap';
    case PeerEducatorAktif = 'peer_educator_aktif';
    case Nonaktif = 'nonaktif';

    public function getLabel(): string
    {
        return match ($this) {
            self::Calon => 'Calon',
            self::Siap => 'Siap',
            self::SiapDenganPenguatan => 'Siap dengan Penguatan',
            self::BelumSiap => 'Belum Siap',
            self::PeerEducatorAktif => 'Peer Educator Aktif',
            self::Nonaktif => 'Nonaktif',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Calon, self::Nonaktif => 'gray',
            self::Siap => 'info',
            self::SiapDenganPenguatan => 'warning',
            self::BelumSiap => 'danger',
            self::PeerEducatorAktif => 'success',
        };
    }
}
