<?php

namespace App\Filament\Resources\Admins;

use App\Filament\Resources\Admins\Pages\CreateAdmin;
use App\Filament\Resources\Admins\Pages\EditAdmin;
use App\Filament\Resources\Admins\Pages\ListAdmins;
use App\Filament\Resources\Admins\Schemas\AdminForm;
use App\Filament\Resources\Admins\Tables\AdminsTable;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'kelola-admin';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('jejak.nav_groups.sistem');
    }

    public static function getNavigationLabel(): string
    {
        return __('jejak.admins.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('jejak.admins.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('jejak.admins.model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return AdminForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdmins::route('/'),
            'create' => CreateAdmin::route('/create'),
            'edit' => EditAdmin::route('/{record}/edit'),
        ];
    }

    public static function temporaryPasswordAction(): Action
    {
        return Action::make('temporaryPassword')
            ->modalHeading(fn (array $arguments): string => __('jejak.admins.temporary_password.heading', ['username' => $arguments['username'] ?? '']))
            ->modalDescription(__('jejak.admins.temporary_password.description'))
            ->fillForm(fn (array $arguments): array => ['password' => $arguments['password'] ?? ''])
            ->schema([
                TextInput::make('password')
                    ->label(__('jejak.admins.temporary_password.label'))
                    ->readOnly()
                    ->copyable(copyMessage: __('jejak.admins.temporary_password.copied')),
            ])
            ->modalSubmitActionLabel(__('jejak.admins.temporary_password.done'))
            ->modalCancelAction(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalCloseButton(false)
            ->action(fn () => null);
    }
}
