<?php

namespace App\Filament\Resources\Admins\Schemas;

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
            ]);
    }
}
