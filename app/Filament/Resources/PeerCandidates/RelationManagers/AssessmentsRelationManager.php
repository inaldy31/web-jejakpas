<?php

namespace App\Filament\Resources\PeerCandidates\RelationManagers;

use App\Filament\Resources\Assessments\AssessmentResource;
use App\Filament\Resources\PeerCandidates\PeerCandidateResource;
use App\Models\Assessment;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('jejak.candidates.assessments.title');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return parent::canViewForRecord($ownerRecord, $pageClass) && PeerCandidateResource::canEdit($ownerRecord);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('assessed_at')->label(__('jejak.candidates.assessments.columns.assessed_at'))->date('d M Y'),
                TextColumn::make('competency_total')->label(__('jejak.candidates.assessments.columns.competency_total')),
                TextColumn::make('competency_level')->label(__('jejak.candidates.assessments.columns.competency_level'))->badge(),
                TextColumn::make('confidence_total')->label(__('jejak.candidates.assessments.columns.confidence_total')),
                TextColumn::make('confidence_level')->label(__('jejak.candidates.assessments.columns.confidence_level'))->badge(),
                TextColumn::make('final_recommendation')->label(__('jejak.candidates.assessments.columns.final_recommendation'))->badge()
                    ->placeholder(__('jejak.candidates.assessments.columns.final_recommendation_placeholder')),
            ])
            ->recordActions([
                Action::make('open')
                    ->label(__('jejak.candidates.assessments.open'))
                    ->url(fn (Assessment $record): string => AssessmentResource::getUrl('edit', ['record' => $record])),
            ])
            ->defaultSort('assessed_at', 'desc');
    }
}
