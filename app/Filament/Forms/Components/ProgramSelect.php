<?php

namespace App\Filament\Forms\Components;

use App\Filament\Forms\Components\Concerns\GuardsArchivedOptions;
use App\Models\Program;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;

class ProgramSelect
{
    use GuardsArchivedOptions;

    public static function make(): Select
    {
        return Select::make('program_id')
            ->label(__('jejak.candidates.program'))
            ->options(fn (Get $get): array => self::scopedOptions(self::scope(...), $get, self::label(...)))
            ->getSearchResultsUsing(fn (Get $get, string $search): array => self::scopedSearchResults(self::scope(...), $get, $search, 'name', self::label(...)))
            ->getOptionLabelUsing(self::archivedAwareLabel(Program::class, self::scope(...), self::label(...)))
            ->searchable()
            ->preload();
    }

    private static function scope(Get $get): Builder
    {
        $query = Program::query()->with('upt')->orderByDesc('year')->orderBy('name');

        return blank($get('upt_id')) ? $query->whereRaw('1 = 0') : $query->where('upt_id', $get('upt_id'));
    }

    private static function label(Program $program): string
    {
        return "{$program->year}: {$program->name} ({$program->upt->short_name})";
    }
}
