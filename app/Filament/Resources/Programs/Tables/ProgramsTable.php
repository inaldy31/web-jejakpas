<?php

namespace App\Filament\Resources\Programs\Tables;

use App\Enums\CompetencyField;
use App\Enums\PublicationStatus;
use App\Filament\Actions\PublicationActions;
use App\Models\Program;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year')->label(__('jejak.programs.columns.year'))->sortable(),
                TextColumn::make('name')->label(__('jejak.programs.columns.name'))->searchable()->wrap(),
                TextColumn::make('upt.short_name')->label(__('jejak.programs.columns.upt')),
                TextColumn::make('competency_field')->label(__('jejak.programs.columns.competency_field'))->badge(),
                TextColumn::make('partner.name')->label(__('jejak.programs.columns.partner'))->placeholder('—'),
                TextColumn::make('total_participants')->label(__('jejak.programs.columns.total_participants'))->numeric()
                    ->placeholder(__('jejak.programs.columns.total_participants_placeholder')),
                TextColumn::make('total_certified')->label(__('jejak.programs.columns.total_certified'))->numeric()
                    ->placeholder(__('jejak.programs.columns.total_certified_placeholder')),
                TextColumn::make('status')->label(__('jejak.programs.columns.status'))->badge(),
            ])
            ->filters([
                SelectFilter::make('upt')->label(__('jejak.programs.filters.upt'))->relationship('upt', 'name'),
                SelectFilter::make('year')->label(__('jejak.programs.filters.year'))
                    ->options(fn (): array => Program::query()->distinct()->orderByDesc('year')->pluck('year', 'year')->all()),
                SelectFilter::make('competency_field')->label(__('jejak.programs.filters.competency_field'))->options(CompetencyField::class),
                SelectFilter::make('partner')->label(__('jejak.programs.filters.partner'))->relationship('partner', 'name'),
                SelectFilter::make('status')->label(__('jejak.programs.filters.status'))->options(PublicationStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([EditAction::make(), ...PublicationActions::record()])
            ->toolbarActions([
                BulkActionGroup::make([
                    PublicationActions::bulkPublish(),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('year', 'desc');
    }
}
