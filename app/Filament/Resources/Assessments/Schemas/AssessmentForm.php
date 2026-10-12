<?php

namespace App\Filament\Resources\Assessments\Schemas;

use App\Enums\CompetencyField;
use App\Enums\Recommendation;
use App\Filament\Forms\Components\CandidateSelect;
use App\Models\Assessment;
use App\Support\FormLimits;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AssessmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('jejak.assessments.sections.identity'))->columns(2)->schema([
                CandidateSelect::make()
                    ->disabledOn('edit'),
                Select::make('competency_field')->label(__('jejak.assessments.competency_field'))->options(CompetencyField::class)->required(),
                DatePicker::make('assessed_at')->label(__('jejak.assessments.assessed_at'))->default(now())->required()
                    ->beforeOrEqual('today')->validationMessages(['before_or_equal' => __('jejak.validation.date_not_future')]),
                TextInput::make('assessor_position')->label(__('jejak.assessments.assessor_position'))->maxLength(255),
                Textarea::make('trainings_attended')->label(__('jejak.assessments.trainings_attended'))->rows(2)->tap(FormLimits::limit(...)),
                Textarea::make('certifications')->label(__('jejak.assessments.certifications'))->rows(2)->tap(FormLimits::limit(...)),
            ]),

            Section::make(__('jejak.assessments.sections.competency'))
                ->description(fn (?Assessment $record): string => self::sectionDescription(__('jejak.assessments.sections.competency_description'), $record))
                ->schema([self::competencyGroup()]),

            Section::make(__('jejak.assessments.sections.confidence'))
                ->description(fn (?Assessment $record): string => self::sectionDescription(__('jejak.assessments.sections.confidence_description'), $record))
                ->schema([self::confidenceGroup()]),

            Section::make(__('jejak.assessments.sections.results'))->visibleOn('edit')->columns(2)->schema([
                TextEntry::make('competency_summary')->label(__('jejak.assessments.competency_summary_label'))
                    ->state(fn (?Assessment $record): ?string => $record?->competencySummary()),
                TextEntry::make('confidence_summary')->label(__('jejak.assessments.confidence_summary_label'))
                    ->state(fn (?Assessment $record): ?string => $record?->confidenceSummary()),
                TextEntry::make('weakest_summary')->label(__('jejak.assessments.weakest_summary_label'))->columnSpanFull()
                    ->state(fn (?Assessment $record): ?string => $record?->weakestSummary()),
                TextEntry::make('suggested_label')->label(__('jejak.assessments.suggested_label'))
                    ->state(fn (?Assessment $record): string => $record?->suggested_recommendation?->getLabel() ?? __('jejak.assessments.suggested_placeholder')),
                TextEntry::make('competency_breakdown')->label(__('jejak.assessments.competency_breakdown_label'))->columnSpanFull()
                    ->listWithLineBreaks()
                    ->state(fn (?Assessment $record): array => $record ? self::breakdownLines($record->competencyDimensionRows()) : []),
                TextEntry::make('confidence_breakdown')->label(__('jejak.assessments.confidence_breakdown_label'))->columnSpanFull()
                    ->listWithLineBreaks()
                    ->state(fn (?Assessment $record): array => $record ? self::breakdownLines($record->confidenceSectionRows()) : []),
            ]),

            Section::make(__('jejak.assessments.sections.recap'))->columns(2)->schema([
                Textarea::make('history_conclusion')->label(__('jejak.assessments.history_conclusion'))->rows(2)->tap(FormLimits::limit(...)),
                Textarea::make('officer_observation')->label(__('jejak.assessments.officer_observation'))->rows(3)->tap(FormLimits::limit(...))->live(onBlur: true)
                    ->required(fn (Get $get): bool => filled($get('final_recommendation')))
                    ->validatedWhenNotDehydrated(false)
                    ->disabled(fn (?Assessment $record): bool => (bool) $record?->hasLockedEvidence())
                    ->helperText(fn (?Assessment $record): string => FormLimits::hint($record?->hasLockedEvidence() ? __('jejak.assessments.finalization.locked') : null)),
                Textarea::make('observation_conclusion')->label(__('jejak.assessments.observation_conclusion'))->rows(2)->tap(FormLimits::limit(...)),
                Select::make('final_recommendation')->label(__('jejak.assessments.final_recommendation'))
                    ->options(Recommendation::class)
                    ->live()
                    ->disabled(fn (Get $get): bool => blank($get('officer_observation')))
                    ->dehydrated()
                    ->helperText(__('jejak.assessments.final_recommendation_helper')),
                TextInput::make('approved_by_position')->label(__('jejak.assessments.approved_by_position'))->maxLength(255)
                    ->required(fn (Get $get): bool => filled($get('final_recommendation'))),
                DatePicker::make('approved_at')->label(__('jejak.assessments.approved_at'))
                    ->required(fn (Get $get): bool => filled($get('final_recommendation')))
                    ->beforeOrEqual('today')->validationMessages(['before_or_equal' => __('jejak.validation.date_not_future')])
                    ->afterOrEqual('assessed_at'),
                Textarea::make('follow_up')->label(__('jejak.assessments.follow_up'))->rows(2)->tap(FormLimits::limit(...))->columnSpanFull(),
            ]),
        ]);
    }

    private static function competencyGroup(): Group
    {
        return Group::make(self::competencyFields())
            ->statePath('competency_scores')
            ->disabled(fn (?Assessment $record): bool => (bool) $record?->hasLockedEvidence())
            ->validatedWhenNotDehydrated(false);
    }

    private static function confidenceGroup(): Group
    {
        return Group::make(self::confidenceFields())
            ->statePath('confidence_scores')
            ->disabled(fn (?Assessment $record): bool => (bool) $record?->hasLockedEvidence())
            ->validatedWhenNotDehydrated(false);
    }

    private static function competencyFields(): array
    {
        return collect(config('jejak.competency_items'))
            ->map(fn (array $item, int $number): Radio => Radio::make((string) $number)
                ->label("{$number}. {$item['indicator']}")
                ->helperText($item['dimension'])
                ->options(config('jejak.competency_scale'))
                ->inline()
                ->required()
                ->validationAttribute(__('jejak.assessments.validation_attributes.competency', ['number' => $number])))
            ->values()
            ->all();
    }

    private static function confidenceFields(): array
    {
        return collect(config('jejak.confidence_items'))
            ->map(fn (array $item, int $number): Radio => Radio::make((string) $number)
                ->label("{$number}. {$item['statement']}")
                ->helperText($item['section'].'. '.config("jejak.confidence_sections.{$item['section']}"))
                ->options(config('jejak.confidence_scale'))
                ->inline()
                ->required()
                ->validationAttribute(__('jejak.assessments.validation_attributes.confidence', ['number' => $number])))
            ->values()
            ->all();
    }

    private static function sectionDescription(string $base, ?Assessment $record): string
    {
        return ((bool) $record?->hasLockedEvidence()) ? "{$base} ".__('jejak.assessments.finalization.locked') : $base;
    }

    private static function breakdownLines(array $rows): array
    {
        return array_map(fn (array $row): string => __('jejak.assessments.dimension_breakdown_line', [
            'label' => $row['label'],
            'subtotal' => (string) $row['subtotal'],
            'max' => (string) $row['max'],
            'average' => number_format($row['average'], 2, ',', '.'),
        ]), $rows);
    }
}
