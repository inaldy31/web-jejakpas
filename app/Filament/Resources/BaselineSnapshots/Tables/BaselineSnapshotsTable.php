<?php

namespace App\Filament\Resources\BaselineSnapshots\Tables;

use App\Enums\SnapshotPhase;
use App\Filament\Actions\PublicationActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BaselineSnapshotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('upt.short_name')->label(__('jejak.snapshots.columns.upt')),
                TextColumn::make('phase')->label(__('jejak.snapshots.columns.phase'))->badge(),
                TextColumn::make('period_label')->label(__('jejak.snapshots.columns.period_label')),
                TextColumn::make('total_participants')->label(__('jejak.snapshots.columns.total_participants'))->numeric()
                    ->placeholder(__('jejak.snapshots.columns.total_participants_placeholder')),
                TextColumn::make('total_certified')->label(__('jejak.snapshots.columns.total_certified'))->numeric()
                    ->placeholder(__('jejak.snapshots.columns.total_certified_placeholder')),
                TextColumn::make('total_peer_educators')->label(__('jejak.snapshots.columns.total_peer_educators'))->numeric()
                    ->placeholder(__('jejak.snapshots.columns.total_peer_educators_placeholder')),
                TextColumn::make('recorded_at')->label(__('jejak.snapshots.columns.recorded_at'))->date('d M Y')->sortable(),
                TextColumn::make('status')->label(__('jejak.snapshots.columns.status'))->badge(),
            ])
            ->filters([
                SelectFilter::make('upt')->label(__('jejak.snapshots.filters.upt'))->relationship('upt', 'name'),
                SelectFilter::make('phase')->label(__('jejak.snapshots.filters.phase'))->options(SnapshotPhase::class),
                TrashedFilter::make(),
            ])
            ->recordActions([EditAction::make(), ...PublicationActions::record()])
            ->toolbarActions([
                BulkActionGroup::make([
                    PublicationActions::bulkPublish(),
                    DeleteBulkAction::make()->authorizeIndividualRecords(),
                    RestoreBulkAction::make()->authorizeIndividualRecords(),
                    ForceDeleteBulkAction::make()->authorizeIndividualRecords(),
                ]),
            ])
            ->defaultSort('recorded_at', 'desc');
    }
}
