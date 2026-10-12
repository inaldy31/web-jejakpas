<?php

namespace App\Filament\Forms\Components\Concerns;

use Closure;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait GuardsArchivedOptions
{
    protected static function scopedOptions(Closure $scope, Get $get, callable $labelUsing): array
    {
        return $scope($get)->get()
            ->mapWithKeys(fn (Model $record): array => [$record->getKey() => $labelUsing($record)])
            ->all();
    }

    protected static function scopedSearchResults(Closure $scope, Get $get, string $search, string|array $searchColumns, callable $labelUsing): array
    {
        return $scope($get)
            ->where(function (Builder $query) use ($searchColumns, $search): void {
                foreach (array_values((array) $searchColumns) as $index => $column) {
                    $index === 0 ? $query->where($column, 'like', "%{$search}%") : $query->orWhere($column, 'like', "%{$search}%");
                }
            })
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (Model $record): array => [$record->getKey() => $labelUsing($record)])
            ->all();
    }

    protected static function archivedAwareLabel(string $modelClass, Closure $scope, callable $labelUsing): Closure
    {
        return function (mixed $value, ?Model $record, Get $get, $component) use ($modelClass, $scope, $labelUsing): ?string {
            $found = $scope($get)->whereKey($value)->first();

            if ($found === null && (string) $record?->getOriginal($component->getName()) === (string) $value) {
                $found = $modelClass::withTrashed()->find($value);
            }

            return $found ? $labelUsing($found) : null;
        };
    }
}
