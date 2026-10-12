<?php

namespace App\Filament\Resources\KnowledgeItems;

use App\Filament\Resources\KnowledgeItems\Pages\CreateKnowledgeItem;
use App\Filament\Resources\KnowledgeItems\Pages\EditKnowledgeItem;
use App\Filament\Resources\KnowledgeItems\Pages\ListKnowledgeItems;
use App\Filament\Resources\KnowledgeItems\Schemas\KnowledgeItemForm;
use App\Filament\Resources\KnowledgeItems\Tables\KnowledgeItemsTable;
use App\Models\KnowledgeItem;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class KnowledgeItemResource extends Resource
{
    protected static ?string $model = KnowledgeItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('jejak.nav_groups.konten');
    }

    public static function getModelLabel(): string
    {
        return __('jejak.knowledge.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('jejak.knowledge.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return KnowledgeItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KnowledgeItemsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKnowledgeItems::route('/'),
            'create' => CreateKnowledgeItem::route('/create'),
            'edit' => EditKnowledgeItem::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function downloadAction(): Action
    {
        return Action::make('download')
            ->label(__('jejak.knowledge.download'))
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->authorize('view')
            ->hidden(fn (KnowledgeItem $record): bool => $record->trashed())
            ->url(fn (KnowledgeItem $record): string => route('admin-files.knowledge-item', $record));
    }
}
