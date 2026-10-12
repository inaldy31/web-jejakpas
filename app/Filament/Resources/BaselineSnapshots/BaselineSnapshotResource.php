<?php

namespace App\Filament\Resources\BaselineSnapshots;

use App\Filament\Resources\BaselineSnapshots\Pages\CreateBaselineSnapshot;
use App\Filament\Resources\BaselineSnapshots\Pages\EditBaselineSnapshot;
use App\Filament\Resources\BaselineSnapshots\Pages\ListBaselineSnapshots;
use App\Filament\Resources\BaselineSnapshots\Schemas\BaselineSnapshotForm;
use App\Filament\Resources\BaselineSnapshots\Tables\BaselineSnapshotsTable;
use App\Models\BaselineSnapshot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class BaselineSnapshotResource extends Resource
{
    protected static ?string $model = BaselineSnapshot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('jejak.nav_groups.pemantauan');
    }

    public static function getModelLabel(): string
    {
        return __('jejak.snapshots.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('jejak.snapshots.model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return BaselineSnapshotForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BaselineSnapshotsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBaselineSnapshots::route('/'),
            'create' => CreateBaselineSnapshot::route('/create'),
            'edit' => EditBaselineSnapshot::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
