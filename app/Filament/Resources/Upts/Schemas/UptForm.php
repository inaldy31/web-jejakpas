<?php

namespace App\Filament\Resources\Upts\Schemas;

use App\Enums\UptType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label(__('jejak.upts.name'))->required()->maxLength(255)->unique(ignoreRecord: true),
            TextInput::make('short_name')->label(__('jejak.upts.short_name'))->required()->maxLength(60),
            Select::make('type')->label(__('jejak.upts.type'))->options(UptType::class)->required(),
            TextInput::make('province')->label(__('jejak.upts.province'))->maxLength(255),
            TagsInput::make('aliases')->label(__('jejak.upts.aliases'))->columnSpanFull(),
            Toggle::make('is_pilot')->label(__('jejak.upts.is_pilot')),
        ]);
    }
}
