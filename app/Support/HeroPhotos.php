<?php

namespace App\Support;

use Illuminate\Support\Collection;

final class HeroPhotos
{
    public static function available(string $configKey): Collection
    {
        return collect(config($configKey, []))
            ->filter(fn (array $photo): bool => is_file(public_path($photo['file'])))
            ->values();
    }
}
