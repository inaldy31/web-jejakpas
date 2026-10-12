<?php

namespace App\Filament\Resources\KnowledgeItems\Tables;

use App\Enums\KnowledgeCategory;
use App\Filament\Actions\PublicationActions;
use App\Filament\Resources\KnowledgeItems\KnowledgeItemResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class KnowledgeItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('jejak.knowledge.columns.title'))->searchable()->wrap(),
                TextColumn::make('category')->label(__('jejak.knowledge.columns.category'))->badge(),
                TextColumn::make('status')->label(__('jejak.knowledge.columns.status'))->badge(),
                TextColumn::make('updated_at')->label(__('jejak.knowledge.columns.updated_at'))->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')->label(__('jejak.knowledge.filters.category'))->options(KnowledgeCategory::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                KnowledgeItemResource::downloadAction(),
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
            ->defaultSort('updated_at', 'desc');
    }
}
