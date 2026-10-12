<?php

namespace App\Filament\Resources\PksDocuments\Tables;

use App\Filament\Actions\PublicationActions;
use App\Filament\Resources\PksDocuments\PksDocumentResource;
use App\Models\PksDocument;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PksDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('jejak.pks_documents.columns.title'))->searchable()->wrap(),
                TextColumn::make('partner.name')->label(__('jejak.pks_documents.columns.partner'))->searchable()
                    ->formatStateUsing(fn (PksDocument $record, ?string $state): ?string => $record->partner?->trashed()
                        ? trim($state.' '.__('jejak.pks_documents.columns.partner_deleted_suffix'))
                        : $state),
                TextColumn::make('pks_number')->label(__('jejak.pks_documents.columns.pks_number')),
                TextColumn::make('valid_until')->label(__('jejak.pks_documents.columns.valid_until'))->date('d M Y')->sortable(),
                TextColumn::make('status')->label(__('jejak.pks_documents.columns.status'))->badge(),
            ])
            ->filters([
                SelectFilter::make('partner')->label(__('jejak.pks_documents.filters.partner'))->relationship('partner', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                PksDocumentResource::downloadAction(),
                EditAction::make(),
                ...PublicationActions::record(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    PublicationActions::bulkPublish(),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('valid_until', 'desc');
    }
}
