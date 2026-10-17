<?php

namespace App\Filament\Pages\Auth;

use App\Support\AccountPasswords;
use Closure;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use SensitiveParameter;

class EditProfile extends BaseEditProfile
{
    private bool $passwordChanged = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getUsernameFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }

    protected function getNameFormComponent(): Component
    {
        return parent::getNameFormComponent()->maxLength(100);
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label(__('jejak.auth.username'))
            ->disabled()
            ->dehydrated(false);
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()->rule(fn (): Closure => AccountPasswords::notCurrentRule($this->getUser()));
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return parent::getCurrentPasswordFormComponent()->visible(fn (Get $get): bool => filled($get('password')));
    }

    protected function mutateFormDataBeforeSave(#[SensitiveParameter] array $data): array
    {
        $this->passwordChanged = array_key_exists('password', $data);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->passwordChanged) {
            AccountPasswords::logChanged($this->getUser());
        }
    }
}
