<?php

namespace App\Filament\Resources\Partners\Schemas;

use App\Enums\CompetencyField;
use App\Enums\PartnerGroup;
use App\Enums\PksStatus;
use App\Enums\SupportType;
use App\Enums\SustainabilityStatus;
use App\Models\Partner;
use App\Models\Upt;
use App\Support\FormLimits;
use App\Support\UptAccess;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('jejak.partners.sections.identity'))->columns(2)->schema([
                TextInput::make('code')->label(__('jejak.partners.code'))->disabled()->dehydrated(false)->placeholder(__('jejak.partners.code_placeholder')),
                TextInput::make('name')->label(__('jejak.partners.name'))->required()->maxLength(255),
                Select::make('group')->label(__('jejak.partners.group'))->options(PartnerGroup::class)->required(),
                TextInput::make('province')->label(__('jejak.partners.province'))->maxLength(255),
                TextInput::make('contact_position')->label(__('jejak.partners.contact_position'))->maxLength(255),
                TextInput::make('contact')->label(__('jejak.partners.contact'))->helperText(__('jejak.partners.contact_helper'))->maxLength(255),
            ]),

            Section::make(__('jejak.partners.sections.legal'))->columns(2)->schema([
                TextInput::make('pks_number')->label(__('jejak.partners.pks_number'))->maxLength(255),
                Select::make('pks_status')->label(__('jejak.partners.pks_status'))->options(PksStatus::class)->required(),
                DatePicker::make('pks_date')->label(__('jejak.partners.pks_date')),
                DatePicker::make('pks_valid_until')->label(__('jejak.partners.pks_valid_until'))->afterOrEqual('pks_date'),
                Textarea::make('scope')->label(__('jejak.partners.scope'))->rows(3)->tap(FormLimits::limit(...))->columnSpanFull(),
            ]),

            Section::make(__('jejak.partners.sections.collaboration'))->schema([
                CheckboxList::make('competency_fields')->label(__('jejak.partners.competency_fields'))->options(CompetencyField::options())->columns(3)->required(),
                CheckboxList::make('support_types')->label(__('jejak.partners.support_types'))->options(SupportType::options())->columns(4)->required(),
                Select::make('upts')->label(__('jejak.partners.upts'))->relationship('upts', 'name')->multiple()->preload()->searchable()
                    ->default(fn (): array => array_filter([UptAccess::userUptId()]))
                    ->disableOptionWhen(fn ($value): bool => UptAccess::userUptId() !== null && (int) $value !== UptAccess::userUptId())
                    ->in(fn (): array => Upt::pluck('id')->all())
                    ->saveRelationshipsUsing(fn (Select $component, ?Partner $record) => $record?->syncUpts(UptAccess::partnerUptsAfterSave($record, (array) $component->getState()))),
            ]),

            Section::make(__('jejak.partners.sections.outcomes'))->columns(3)->schema([
                self::countInput('total_trainings'),
                self::countInput('total_participants'),
                self::countInput('total_certified'),
                self::countInput('total_peer_educators'),
                self::countInput('total_beneficiaries'),
                TextInput::make('avg_confidence_score')->label(__('jejak.partners.avg_confidence_score'))->numeric()->minValue(20)->maxValue(100)->helperText(__('jejak.partners.avg_confidence_score_helper')),
                Select::make('sustainability_status')->label(__('jejak.partners.sustainability_status'))->options(SustainabilityStatus::class)->required(),
                TextInput::make('liaison_position')->label(__('jejak.partners.liaison_position'))->maxLength(255),
                Textarea::make('best_practice_summary')->label(__('jejak.partners.best_practice_summary'))->rows(3)->tap(FormLimits::limit(...))->columnSpanFull(),
            ]),
        ]);
    }

    private static function countInput(string $field): TextInput
    {
        return TextInput::make($field)->label(__('jejak.partners.'.$field))->integer()->minValue(0)->maxValue(Partner::MAX_COUNT)->helperText(self::blankHint());
    }

    private static function blankHint(): string
    {
        return __('jejak.partners.blank_hint');
    }
}
