<?php

namespace App\Filament\Resources\PeerCandidates\Schemas;

use App\Enums\CandidateStatus;
use App\Enums\CompetencyField;
use App\Filament\Forms\Components\ProgramSelect;
use App\Support\FormLimits;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PeerCandidateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('code')->label(__('jejak.candidates.code'))->disabled()->dehydrated(false)
                ->placeholder(__('jejak.candidates.code_placeholder')),
            Select::make('upt_id')->label(__('jejak.candidates.upt'))->relationship('upt', 'name')->preload()->required()
                ->helperText(fn (string $operation): ?string => $operation === 'edit' ? __('jejak.candidates.upt_helper') : null)
                ->live(),
            TextInput::make('label')->label(__('jejak.candidates.label'))->required()->maxLength(50)
                ->helperText(__('jejak.candidates.label_helper'))
                ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                    $trimmed = trim((string) $value);

                    if ($trimmed === '') {
                        return;
                    }

                    $letterCount = preg_match_all('/[A-Za-z]/', $trimmed);

                    if (! preg_match('/^[A-Za-z. ]+$/', $trimmed) || $letterCount < 1 || $letterCount > 5) {
                        $fail(__('jejak.candidates.label_invalid'));
                    }
                })
                ->dehydrateStateUsing(fn (?string $state): ?string => is_string($state) ? Str::upper(trim($state)) : $state),
            Select::make('competency_field')->label(__('jejak.candidates.competency_field'))->options(CompetencyField::class)->required(),
            ProgramSelect::make(),
            Select::make('current_status')->label(__('jejak.candidates.status'))->options(CandidateStatus::class)
                ->disabled()->dehydrated(false)->visibleOn('edit'),
            DatePicker::make('designated_at')->label(__('jejak.candidates.designated_at'))->disabled()->dehydrated(false)->visibleOn('edit'),
            TextInput::make('approved_by_position')->label(__('jejak.candidates.approved_by_position'))->disabled()->dehydrated(false)->visibleOn('edit'),
            Textarea::make('notes')->label(__('jejak.candidates.notes'))->rows(3)->tap(FormLimits::limit(...))->columnSpanFull(),
        ]);
    }
}
