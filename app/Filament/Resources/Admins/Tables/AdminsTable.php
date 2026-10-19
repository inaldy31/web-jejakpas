<?php

namespace App\Filament\Resources\Admins\Tables;

use App\Models\User;
use App\Support\AdminAccounts;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdminsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('jejak.admins.fields.name'))->searchable()->sortable(),
                TextColumn::make('username')->label(__('jejak.auth.username'))->searchable()->sortable(),
                TextColumn::make('upt.name')->label(__('jejak.admins.fields.upt'))->placeholder(__('jejak.admins.fields.upt_central')),
                TextColumn::make('status')->label(__('jejak.admins.columns.status'))->badge()
                    ->state(fn (User $record): string => $record->isActive() ? __('jejak.admins.status.active') : __('jejak.admins.status.deactivated'))
                    ->color(fn (User $record): string => $record->isActive() ? 'success' : 'gray'),
                TextColumn::make('two_factor')->label(__('jejak.admins.columns.two_factor'))->badge()
                    ->state(fn (User $record): string => $record->hasTwoFactor() ? __('jejak.admins.two_factor.enabled') : __('jejak.admins.two_factor.disabled'))
                    ->color(fn (User $record): string => $record->hasTwoFactor() ? 'success' : 'warning'),
                TextColumn::make('is_primary_admin')->label(__('jejak.admins.columns.role'))->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? __('jejak.admins.roles.primary') : __('jejak.admins.roles.admin'))
                    ->color(fn (bool $state): string => $state ? 'primary' : 'gray'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('deactivate')
                    ->label(__('jejak.admins.actions.deactivate.label'))
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription(__('jejak.admins.actions.deactivate.description'))
                    ->authorize('deactivate')
                    ->action(function (User $record): void {
                        $record->deactivated_at = now();
                        $record->save();

                        AdminAccounts::log('deactivated', $record, self::actor());
                        self::notifyDone('deactivate', $record);
                    }),
                Action::make('reactivate')
                    ->label(__('jejak.admins.actions.reactivate.label'))
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->requiresConfirmation()
                    ->authorize('reactivate')
                    ->action(function (User $record): void {
                        $record->deactivated_at = null;
                        $record->save();

                        AdminAccounts::log('reactivated', $record, self::actor());
                        self::notifyDone('reactivate', $record);
                    }),
                Action::make('resetTwoFactor')
                    ->label(__('jejak.admins.actions.reset_two_factor.label'))
                    ->icon(Heroicon::OutlinedDevicePhoneMobile)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription(__('jejak.admins.actions.reset_two_factor.description'))
                    ->authorize('resetTwoFactor')
                    ->action(function (User $record): void {
                        AdminAccounts::resetTwoFactor($record);
                        AdminAccounts::log('two_factor_reset', $record, self::actor());
                        self::notifyDone('reset_two_factor', $record);
                    }),
                Action::make('resetPassword')
                    ->label(__('jejak.admins.actions.reset_password.label'))
                    ->icon(Heroicon::OutlinedKey)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription(__('jejak.admins.actions.reset_password.description'))
                    ->authorize('resetPassword')
                    ->action(function (User $record, Component $livewire): void {
                        $password = AdminAccounts::resetPassword($record);
                        AdminAccounts::log('password_reset', $record, self::actor());

                        $livewire->replaceMountedAction('temporaryPassword', ['password' => $password, 'username' => $record->username]);
                    }),
            ])
            ->defaultSort('name');
    }

    private static function notifyDone(string $action, User $record): void
    {
        Notification::make()->title(__("jejak.admins.actions.{$action}.success", ['username' => $record->username]))->success()->send();
    }

    private static function actor(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }
}
