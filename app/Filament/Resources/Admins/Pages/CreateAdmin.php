<?php

namespace App\Filament\Resources\Admins\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Admins\AdminResource;
use App\Models\User;
use App\Support\AdminAccounts;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    protected static bool $canCreateAnother = false;

    private ?string $temporaryPassword = null;

    protected function handleRecordCreation(array $data): Model
    {
        $this->temporaryPassword = AdminAccounts::temporaryPassword();

        $user = new User;
        $user->forceFill([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => null,
            'password' => $this->temporaryPassword,
            'role' => UserRole::Admin,
            'must_change_password' => true,
        ])->save();

        $creator = Auth::user();
        AdminAccounts::log('created', $user, $creator instanceof User ? $creator : null, ['name' => $user->name, 'username' => $user->username]);

        return $user;
    }

    protected function afterCreate(): void
    {
        $this->mountAction('temporaryPassword', ['password' => $this->temporaryPassword, 'username' => $this->getRecord()->username]);

        $this->halt();
    }

    public function temporaryPasswordAction(): Action
    {
        return AdminResource::temporaryPasswordAction()
            ->action(fn () => $this->redirect(static::getResource()::getUrl('index')));
    }
}
