<?php

namespace App\Filament\Resources\PksDocuments;

use App\Filament\Resources\PksDocuments\Pages\CreatePksDocument;
use App\Filament\Resources\PksDocuments\Pages\EditPksDocument;
use App\Filament\Resources\PksDocuments\Pages\ListPksDocuments;
use App\Filament\Resources\PksDocuments\Schemas\PksDocumentForm;
use App\Filament\Resources\PksDocuments\Tables\PksDocumentsTable;
use App\Models\PksDocument;
use App\Support\UptAccess;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PksDocumentResource extends Resource
{
    protected static ?string $model = PksDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('jejak.nav_groups.data_mitra');
    }

    public static function getModelLabel(): string
    {
        return __('jejak.pks_documents.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('jejak.pks_documents.model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return PksDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PksDocumentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPksDocuments::route('/'),
            'create' => CreatePksDocument::route('/create'),
            'edit' => EditPksDocument::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return UptAccess::restrict(parent::getEloquentQuery());
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function downloadAction(): Action
    {
        return Action::make('download')
            ->label(__('jejak.pks_documents.download'))
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->authorize('view')
            ->hidden(fn (PksDocument $record): bool => $record->trashed())
            ->url(fn (PksDocument $record): string => route('admin-files.pks-document', $record));
    }
}
