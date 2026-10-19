<?php

namespace App\Filament\Resources\BaselineSnapshots\Schemas;

use App\Enums\SnapshotPhase;
use App\Models\BaselineSnapshot;
use App\Models\Partner;
use App\Support\FormLimits;
use App\Support\UptAccess;
use Closure;
use DateTime;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class BaselineSnapshotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('jejak.snapshots.sections.period'))->columns(2)->schema([
                Select::make('upt_id')->label(__('jejak.snapshots.upt'))->relationship('upt', 'name')->preload()->required()
                    ->default(fn () => UptAccess::userUptId())->disabled(fn () => UptAccess::userUptId() !== null)->dehydrated(),
                ToggleButtons::make('phase')->label(__('jejak.snapshots.phase'))->options(SnapshotPhase::class)->inline()->required(),
                TextInput::make('period_label')->label(__('jejak.snapshots.period_label'))->required()->maxLength(60)
                    ->helperText(__('jejak.snapshots.period_label_helper')),
                DatePicker::make('recorded_at')->label(__('jejak.snapshots.recorded_at'))->required()
                    ->beforeOrEqual('today')->validationMessages(['before_or_equal' => __('jejak.validation.date_not_future')])
                    ->helperText(__('jejak.snapshots.recorded_at_helper'))
                    ->rule(fn (Get $get, ?BaselineSnapshot $record): Closure => self::notBeforeLatestBaseline($get, $record)),
            ]),
            Section::make(__('jejak.snapshots.sections.indicators'))->columns(3)->schema([
                TextInput::make('total_trainings')->label(__('jejak.snapshots.total_trainings'))->integer()->minValue(0)->maxValue(Partner::MAX_COUNT)
                    ->helperText(__('jejak.snapshots.blank_hint')),
                TextInput::make('total_participants')->label(__('jejak.snapshots.total_participants'))->integer()->minValue(0)->maxValue(Partner::MAX_COUNT)
                    ->helperText(__('jejak.snapshots.blank_hint')),
                TextInput::make('total_certified')->label(__('jejak.snapshots.total_certified'))->integer()->minValue(0)->maxValue(Partner::MAX_COUNT)
                    ->helperText(__('jejak.snapshots.blank_hint')),
                TextInput::make('total_assessed')->label(__('jejak.snapshots.total_assessed'))->integer()->minValue(0)->maxValue(Partner::MAX_COUNT)
                    ->requiredWith('avg_competency_score,avg_confidence_score')
                    ->validationMessages(['required_with' => __('jejak.snapshots.total_assessed_required')])
                    ->helperText(__('jejak.snapshots.blank_hint')),
                TextInput::make('total_peer_educators')->label(__('jejak.snapshots.total_peer_educators'))->integer()->minValue(0)->maxValue(Partner::MAX_COUNT)
                    ->helperText(__('jejak.snapshots.blank_hint')),
                TextInput::make('total_beneficiaries')->label(__('jejak.snapshots.total_beneficiaries'))->integer()->minValue(0)->maxValue(Partner::MAX_COUNT)
                    ->helperText(__('jejak.snapshots.blank_hint')),
                TextInput::make('avg_competency_score')->label(__('jejak.snapshots.avg_competency_score'))->numeric()->minValue(12)->maxValue(60)
                    ->rule('decimal:0,2')
                    ->helperText(self::averageHelper(__('jejak.snapshots.avg_competency_score_helper'))),
                TextInput::make('avg_confidence_score')->label(__('jejak.snapshots.avg_confidence_score'))->numeric()->minValue(20)->maxValue(100)
                    ->rule('decimal:0,2')
                    ->helperText(self::averageHelper(__('jejak.snapshots.avg_confidence_score_helper'))),
                TextInput::make('active_partners')->label(__('jejak.snapshots.active_partners'))->integer()->minValue(0)->maxValue(Partner::MAX_COUNT)
                    ->helperText(__('jejak.snapshots.blank_hint')),
                Textarea::make('notes')->label(__('jejak.snapshots.notes'))->rows(3)->tap(FormLimits::limit(...))->columnSpanFull(),
            ]),
        ]);
    }

    private static function notBeforeLatestBaseline(Get $get, ?BaselineSnapshot $record): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
            $phase = $get('phase');
            $phase = $phase instanceof SnapshotPhase ? $phase : SnapshotPhase::tryFrom((string) $phase);

            $uptId = UptAccess::userUptId() ?? $get('upt_id');

            if ($phase !== SnapshotPhase::Post || blank($uptId)) {
                return;
            }

            if ($record !== null
                && $record->phase === $phase
                && (string) $record->upt_id === (string) $uptId
                && $record->recorded_at?->toDateString() === $value) {
                return;
            }

            $recordedAt = is_string($value) ? DateTime::createFromFormat('!Y-m-d', $value) : false;

            if (! $recordedAt || $recordedAt->format('Y-m-d') !== $value) {
                return;
            }

            $baseline = BaselineSnapshot::query()
                ->where('upt_id', $uptId)
                ->where('phase', SnapshotPhase::Baseline->value)
                ->orderByDesc('recorded_at')
                ->first();

            if ($baseline !== null && Carbon::instance($recordedAt)->lt($baseline->recorded_at)) {
                $fail(__('jejak.snapshots.post_before_baseline', ['date' => $baseline->recorded_at->translatedFormat('d F Y')]));
            }
        };
    }

    private static function averageHelper(string $scale): string
    {
        return $scale.' '.__('jejak.snapshots.average_privacy_helper', [
            'count' => (int) config('jejak.privacy.min_group_size'),
        ]);
    }
}
