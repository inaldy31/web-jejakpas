<?php

namespace App\Filament\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class LocalAvatarProvider implements AvatarProvider
{
    private const BACKGROUND = '#05213d';

    private const TEXT = '#f1bd58';

    public function get(Model|Authenticatable $record): string
    {
        $initials = collect(preg_split('/\s+/u', trim(Filament::getNameForDefaultAvatar($record)), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn (string $word): string => mb_substr(preg_replace('/^[^\p{L}\p{N}]+/u', '', $word), 0, 1))
            ->filter()
            ->take(2)
            ->join('');

        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" fill="%s"/><text x="32" y="32" dy=".35em" text-anchor="middle" fill="%s" font-family="ui-sans-serif, system-ui, sans-serif" font-size="26" font-weight="600">%s</text></svg>',
            self::BACKGROUND,
            self::TEXT,
            htmlspecialchars(mb_strtoupper($initials) ?: '?', ENT_XML1 | ENT_QUOTES, 'UTF-8'),
        );

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
