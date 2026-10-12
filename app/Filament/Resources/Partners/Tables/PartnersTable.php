<?php

namespace App\Filament\Resources\Partners\Tables;

use App\Enums\CompetencyField;
use App\Enums\PartnerGroup;
use App\Enums\PksStatus;
use App\Enums\PublicationStatus;
use App\Enums\SustainabilityStatus;
use App\Filament\Actions\PublicationActions;
use App\Models\Partner;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label(__('jejak.partners.columns.code'))->sortable()->searchable(),
                TextColumn::make('name')->label(__('jejak.partners.columns.name'))->sortable()->searchable(),
                TextColumn::make('pks_status')->label(__('jejak.partners.columns.pks_status'))->badge()->placeholder(__('jejak.partners.columns.pks_status_placeholder'))
                    ->formatStateUsing(fn (PksStatus $state, Partner $record): string => $record->hasExpiredActivePks() ? __('jejak.partners.columns.pks_status_expired') : $state->getLabel())
                    ->color(fn (PksStatus $state, Partner $record): string => $record->hasExpiredActivePks() ? 'warning' : $state->getColor()),
                TextColumn::make('group')->label(__('jejak.partners.columns.group'))->badge()->color('gray'),
                TextColumn::make('competency_fields')->label(__('jejak.partners.columns.competency_fields'))->badge()
                    ->formatStateUsing(fn (string $state): string => CompetencyField::from($state)->getLabel()),
                TextColumn::make('upts.short_name')->label(__('jejak.partners.columns.upts'))->badge(),
                TextColumn::make('status')->label(__('jejak.partners.columns.status'))->badge(),
                TextColumn::make('updated_at')->label(__('jejak.partners.columns.updated_at'))->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('group')->label(__('jejak.partners.filters.group'))->options(PartnerGroup::class),
                SelectFilter::make('pks_status')->label(__('jejak.partners.filters.pks_status'))->options(PksStatus::class),
                SelectFilter::make('sustainability_status')->label(__('jejak.partners.filters.sustainability_status'))->options(SustainabilityStatus::class),
                SelectFilter::make('competency')->label(__('jejak.partners.filters.competency'))
                    ->options(CompetencyField::options())
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->withCompetency($data['value'])
                        : $query),
                SelectFilter::make('upts')->label(__('jejak.partners.filters.upts'))->relationship('upts', 'name'),
                SelectFilter::make('status')->label(__('jejak.partners.filters.status'))->options(PublicationStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
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
            ->defaultSort(fn (Builder $query, string $direction): Builder => $query
                ->orderByRaw('LENGTH(code) '.($direction === 'desc' ? 'desc' : 'asc'))
                ->orderBy('code', $direction));
    }
}
