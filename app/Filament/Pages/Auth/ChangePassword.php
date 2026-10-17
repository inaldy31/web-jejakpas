<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Support\AccountPasswords;
use Closure;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\Rules\Password;

class ChangePassword extends SimplePage
{
    use WithRateLimiting;

    public ?array $data = [];

    public function mount(): void
    {
        if (! $this->getUser()->must_change_password) {
            redirect()->to(Filament::getUrl());

            return;
        }

        $this->form->fill();
    }

    public function getTitle(): string|Htmlable
    {
        return __('jejak.account.change_password.heading');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('jejak.account.change_password.subheading');
    }

    public function form(Schema $schema): Schema
    {
        $user = $this->getUser();

        return $schema
            ->components([
                TextInput::make('currentPassword')
                    ->label(__('jejak.account.change_password.current'))
                    ->password()
                    ->revealable()
                    ->autocomplete('current-password')
                    ->currentPassword(guard: Filament::getAuthGuard())
                    ->required(),
                TextInput::make('password')
                    ->label(__('jejak.account.change_password.new'))
                    ->password()
                    ->revealable()
                    ->autocomplete('new-password')
                    ->required()
                    ->rule(Password::default())
                    ->rule(fn (): Closure => AccountPasswords::notCurrentRule($user))
                    ->same('passwordConfirmation'),
                TextInput::make('passwordConfirmation')
                    ->label(__('jejak.account.change_password.confirmation'))
                    ->password()
                    ->revealable()
                    ->autocomplete('new-password')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')->label(__('jejak.account.change_password.submit'))->submit('save'),
                        ])->fullWidth(),
                    ]),
            ]);
    }

    public function save(): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::auth/pages/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => $exception->minutesUntilAvailable,
                ]))
                ->danger()
                ->send();

            return;
        }

        $data = $this->form->getState();
        $user = $this->getUser();

        $user->password = $data['password'];
        $user->must_change_password = false;
        $user->save();

        session()->put('password_hash_'.Filament::getAuthGuard(), $user->getAuthPassword());

        AccountPasswords::logChanged($user);

        $this->redirectIntended(Filament::getUrl());
    }

    private function getUser(): User
    {
        return Filament::auth()->user();
    }
}
