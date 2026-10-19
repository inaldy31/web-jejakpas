<?php

namespace App\Filament\Forms\Components;

use App\Filament\Forms\Components\Concerns\GuardsArchivedOptions;
use App\Models\Partner;
use App\Support\UptAccess;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;

class PartnerSelect
{
    use GuardsArchivedOptions;

    public static function make(?string $label = null): Select
    {
        return Select::make('partner_id')
            ->label($label ?? __('jejak.partners.model_label'))
            ->options(fn (Get $get): array => self::scopedOptions(self::scope(...), $get, self::label(...)))
            ->getSearchResultsUsing(fn (Get $get, string $search): array => self::scopedSearchResults(self::scope(...), $get, $search, 'name', self::label(...)))
            ->getOptionLabelUsing(self::archivedAwareLabel(Partner::class, self::scope(...), self::label(...)))
            ->searchable()
            ->preload();
    }

    private static function scope(Get $get): Builder
    {
        return UptAccess::restrict(Partner::query()->orderBy('name'));
    }

    private static function label(Partner $partner): string
    {
        return $partner->name;
    }
}
