<?php

namespace App\Support;

use InvalidArgumentException;

final class StatIcons
{
    private const ICONS = [
        'pilot_upts' => 'heroicon-o-map-pin',
        'partners' => 'heroicon-o-building-office-2',
        'programs' => 'heroicon-o-book-open',
        'participants' => 'heroicon-o-user-group',
        'certified' => 'heroicon-o-check-badge',
        'assessed' => 'heroicon-o-clipboard-document-check',
        'ready' => 'heroicon-o-check-circle',
        'peer_educators' => 'heroicon-o-academic-cap',
        'beneficiaries' => 'heroicon-o-heart',
    ];

    public static function for(string $key): string
    {
        return self::ICONS[$key] ?? throw new InvalidArgumentException("Tidak ada ikon statistik untuk \"{$key}\".");
    }
}
