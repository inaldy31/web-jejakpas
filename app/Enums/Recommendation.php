<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum Recommendation: string implements HasLabel
{
    use HasOptions;

    case Siap = 'siap';
    case SiapDenganPenguatan = 'siap_dengan_penguatan';
    case BelumSiap = 'belum_siap';

    public function candidateStatus(): CandidateStatus
    {
        return match ($this) {
            self::Siap => CandidateStatus::Siap,
            self::SiapDenganPenguatan => CandidateStatus::SiapDenganPenguatan,
            self::BelumSiap => CandidateStatus::BelumSiap,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Siap => 'Siap',
            self::SiapDenganPenguatan => 'Siap dengan Penguatan',
            self::BelumSiap => 'Belum Siap',
        };
    }
}
