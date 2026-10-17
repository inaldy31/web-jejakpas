<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Models\Activity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    private const EVENTS = [
        'login', 'logout', 'login_failed', 'two_factor_failed', 'two_factor_unreadable',
        'created', 'updated', 'deleted', 'restored',
        'published', 'unpublished',
        'downloaded', 'profile_printed', 'deactivated', 'reactivated', 'upts_updated',
        'password_changed', 'two_factor_enabled', 'two_factor_disabled', 'two_factor_reset', 'password_reset', 'primary_admin_assigned',
    ];

    private const SUBJECT_LABEL_KEYS = [
        'Upt' => 'jejak.upts.model_label',
        'Partner' => 'jejak.partners.model_label',
        'Program' => 'jejak.programs.model_label',
        'BaselineSnapshot' => 'jejak.snapshots.model_label',
        'PksDocument' => 'jejak.pks_documents.model_label',
        'PeerCandidate' => 'jejak.candidates.model_label',
        'Assessment' => 'jejak.assessments.model_label',
        'KnowledgeItem' => 'jejak.knowledge.model_label',
        'User' => 'jejak.admins.model_label',
    ];

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('jejak.nav_groups.sistem');
    }

    public static function getModelLabel(): string
    {
        return __('jejak.activities.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('jejak.activities.model_label');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('causer'))
            ->columns([
                TextColumn::make('created_at')->label(__('jejak.activities.columns.created_at'))->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('causer.name')->label(__('jejak.activities.columns.causer'))
                    ->placeholder(fn (Activity $record): string => in_array($record->event, ['login_failed', 'two_factor_failed', 'two_factor_unreadable'], true) ? __('jejak.activities.unknown_causer') : __('jejak.activities.system_placeholder')),
                TextColumn::make('event')->label(__('jejak.activities.columns.event'))->badge()
                    ->formatStateUsing(fn (string $state): string => static::eventLabels()[$state] ?? $state),
                TextColumn::make('subject_type')->label(__('jejak.activities.columns.subject_type'))->placeholder(__('jejak.activities.unknown_subject'))
                    ->formatStateUsing(fn (string $state): string => static::subjectLabel($state)),
                TextColumn::make('subject_id')->label(__('jejak.activities.columns.subject_id')),
                TextColumn::make('log_name')->label(__('jejak.activities.columns.log_name'))->placeholder(__('jejak.activities.unknown_subject'))
                    ->formatStateUsing(fn (string $state): string => static::categoryLabel($state)),
            ])
            ->filters([
                SelectFilter::make('event')->label(__('jejak.activities.filters.event'))->options(static::eventLabels()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListActivities::route('/')];
    }

    private static function eventLabels(): array
    {
        return collect(self::EVENTS)
            ->mapWithKeys(fn (string $event): array => [$event => __('jejak.activity_events.'.$event)])
            ->all();
    }

    private static function subjectLabel(string $subjectType): string
    {
        $basename = class_basename($subjectType);
        $labelKey = static::SUBJECT_LABEL_KEYS[$basename] ?? null;

        return $labelKey ? __($labelKey) : $basename;
    }

    private static function categoryLabel(string $logName): string
    {
        $label = __('jejak.activity_categories.'.$logName);

        return $label === 'jejak.activity_categories.'.$logName ? $logName : $label;
    }
}
