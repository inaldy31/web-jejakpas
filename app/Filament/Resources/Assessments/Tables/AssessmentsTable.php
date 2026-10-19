<?php

namespace App\Filament\Resources\Assessments\Tables;

use App\Enums\Recommendation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AssessmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('candidate.code')->label(__('jejak.assessments.columns.candidate'))->searchable(),
                TextColumn::make('candidate.label')->label(__('jejak.assessments.columns.label')),
                TextColumn::make('upt.short_name')->label(__('jejak.assessments.columns.upt')),
                TextColumn::make('assessed_at')->label(__('jejak.assessments.columns.assessed_at'))->date('d M Y')->sortable(),
                TextColumn::make('competency_total')->label(__('jejak.assessments.columns.competency_total'))->sortable(),
                TextColumn::make('confidence_total')->label(__('jejak.assessments.columns.confidence_total'))->sortable(),
                TextColumn::make('suggested_recommendation')->label(__('jejak.assessments.columns.suggested_recommendation'))->badge()
                    ->placeholder(__('jejak.assessments.columns.suggested_recommendation_placeholder')),
                TextColumn::make('final_recommendation')->label(__('jejak.assessments.columns.final_recommendation'))->badge()
                    ->placeholder(__('jejak.assessments.columns.final_recommendation_placeholder')),
            ])
            ->filters([
                SelectFilter::make('upt')->label(__('jejak.assessments.filters.upt'))->relationship('upt', 'name'),
                SelectFilter::make('final_recommendation')->label(__('jejak.assessments.filters.final_recommendation'))->options(Recommendation::class),
                TrashedFilter::make(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->authorizeIndividualRecords(),
                    RestoreBulkAction::make()->authorizeIndividualRecords(),
                    ForceDeleteBulkAction::make()->authorizeIndividualRecords(),
                ]),
            ])
            ->defaultSort('assessed_at', 'desc');
    }
}
