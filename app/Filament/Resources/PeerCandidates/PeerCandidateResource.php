<?php

namespace App\Filament\Resources\PeerCandidates;

use App\Filament\Resources\PeerCandidates\Pages\CreatePeerCandidate;
use App\Filament\Resources\PeerCandidates\Pages\EditPeerCandidate;
use App\Filament\Resources\PeerCandidates\Pages\ListPeerCandidates;
use App\Filament\Resources\PeerCandidates\RelationManagers\AssessmentsRelationManager;
use App\Filament\Resources\PeerCandidates\Schemas\PeerCandidateForm;
use App\Filament\Resources\PeerCandidates\Tables\PeerCandidatesTable;
use App\Models\PeerCandidate;
use App\Support\UptAccess;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PeerCandidateResource extends Resource
{
    protected static ?string $model = PeerCandidate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'code';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('jejak.nav_groups.program_warga_binaan');
    }

    public static function getModelLabel(): string
    {
        return __('jejak.candidates.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('jejak.candidates.model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return PeerCandidateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PeerCandidatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [AssessmentsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPeerCandidates::route('/'),
            'create' => CreatePeerCandidate::route('/create'),
            'edit' => EditPeerCandidate::route('/{record}/edit'),
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
}
