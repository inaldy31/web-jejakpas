<?php

namespace App\Filament\Forms\Components;

use App\Filament\Forms\Components\Concerns\GuardsArchivedOptions;
use App\Models\PeerCandidate;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;

class CandidateSelect
{
    use GuardsArchivedOptions;

    public static function make(): Select
    {
        return Select::make('peer_candidate_id')
            ->label(__('jejak.assessments.candidate'))
            ->options(fn (Get $get): array => self::scopedOptions(self::scope(...), $get, self::label(...)))
            ->getSearchResultsUsing(fn (Get $get, string $search): array => self::scopedSearchResults(self::scope(...), $get, $search, ['code', 'label'], self::label(...)))
            ->getOptionLabelUsing(self::archivedAwareLabel(PeerCandidate::class, self::scope(...), self::label(...)))
            ->searchable()
            ->preload()
            ->required();
    }

    private static function scope(Get $get): Builder
    {
        return PeerCandidate::query()->with('upt')->orderBy('code');
    }

    private static function label(PeerCandidate $candidate): string
    {
        return "{$candidate->code}: {$candidate->label} ({$candidate->upt->short_name})";
    }
}
