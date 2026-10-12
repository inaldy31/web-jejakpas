<?php

namespace App\Filament\Resources\Programs\Schemas;

use App\Enums\CompetencyField;
use App\Filament\Forms\Components\PartnerSelect;
use App\Models\Partner;
use App\Support\FormLimits;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('upt_id')->label(__('jejak.programs.upt'))->relationship('upt', 'name')->preload()->required(),
            TextInput::make('year')->label(__('jejak.programs.year'))->integer()->minValue(2000)->maxValue(2100)->required(),
            TextInput::make('name')->label(__('jejak.programs.name'))->required()->maxLength(255),
            Select::make('competency_field')->label(__('jejak.programs.competency_field'))->options(CompetencyField::class)->required(),
            PartnerSelect::make(__('jejak.programs.partner')),
            TextInput::make('instructor_note')->label(__('jejak.programs.instructor_note'))->maxLength(255),
            TextInput::make('total_participants')->label(__('jejak.programs.total_participants'))->integer()->minValue(0)->maxValue(Partner::MAX_COUNT),
            TextInput::make('total_certified')->label(__('jejak.programs.total_certified'))->integer()->minValue(0)->maxValue(Partner::MAX_COUNT)
                ->helperText(__('jejak.programs.total_certified_helper')),
            Textarea::make('notes')->label(__('jejak.programs.notes'))->rows(2)->tap(FormLimits::limit(...))->columnSpanFull(),
        ]);
    }
}
