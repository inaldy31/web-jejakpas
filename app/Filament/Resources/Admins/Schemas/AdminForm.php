<?php

namespace App\Filament\Resources\Admins\Schemas;

use App\Models\Upt;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('jejak.admins.fields.name'))
                    ->required()
                    ->maxLength(100),
                TextInput::make('username')
                    ->label(__('jejak.auth.username'))
                    ->helperText(__('jejak.admins.fields.username_help'))
                    ->required()
                    ->maxLength(50)
                    ->mutateStateForValidationUsing(fn (mixed $state): mixed => is_string($state) ? Str::lower(trim($state)) : $state)
                    ->dehydrateStateUsing(fn (string $state): string => Str::lower(trim($state)))
                    ->regex('/^[a-z0-9._-]+$/')
                    ->validationMessages(['regex' => __('jejak.admins.fields.username_format')])
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit'),
                Select::make('upt_id')
                    ->label(__('jejak.admins.fields.upt'))
                    ->options(fn (): array => Upt::orderBy('name')->pluck('name', 'id')->all())
                    ->placeholder(__('jejak.admins.fields.upt_central'))
                    ->helperText(__('jejak.admins.fields.upt_help'))
                    ->hidden(fn (?User $record): bool => (bool) $record?->is_primary_admin),
            ]);
    }
}
