<?php

namespace App\Filament\Resources\Upts\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('jejak.upts.name_column'))->searchable()->sortable(),
                TextColumn::make('type')->label(__('jejak.upts.type'))->badge(),
                TextColumn::make('province')->label(__('jejak.upts.province'))->sortable(),
                IconColumn::make('is_pilot')->label(__('jejak.upts.is_pilot_column'))->boolean(),
            ])
            ->recordActions([EditAction::make()])
            ->defaultSort('name');
    }
}
