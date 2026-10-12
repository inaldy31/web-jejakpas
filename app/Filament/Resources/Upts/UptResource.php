<?php

namespace App\Filament\Resources\Upts;

use App\Filament\Resources\Upts\Pages\CreateUpt;
use App\Filament\Resources\Upts\Pages\EditUpt;
use App\Filament\Resources\Upts\Pages\ListUpts;
use App\Filament\Resources\Upts\Schemas\UptForm;
use App\Filament\Resources\Upts\Tables\UptsTable;
use App\Models\Upt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UptResource extends Resource
{
    protected static ?string $model = Upt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('jejak.nav_groups.sistem');
    }

    public static function getModelLabel(): string
    {
        return __('jejak.upts.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('jejak.upts.model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return UptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UptsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUpts::route('/'),
            'create' => CreateUpt::route('/create'),
            'edit' => EditUpt::route('/{record}/edit'),
        ];
    }
}
