<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;
use Filament\Support\Contracts\HasLabel;

enum SnapshotPhase: string implements HasLabel
{
    use HasOptions;

    case Baseline = 'baseline';
    case Post = 'post';

    public function getLabel(): string
    {
        return match ($this) {
            self::Baseline => 'Sebelum implementasi (baseline)',
            self::Post => 'Sesudah implementasi',
        };
    }
}
