<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Auth\TrustedDeviceChallenge;
use App\Models\User;
use App\Support\TrustedDevices;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\MultiFactor\MultiFactorChallenge;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $response = parent::authenticate();
        } catch (ValidationException $exception) {
            $this->logWrongCode($exception);

            throw $exception;
        }

        $user = Filament::auth()->user();

        if (
            $response !== null
            && $user instanceof User
            && filled($this->userUndertakingMultiFactorAuthentication)
            && data_get($this->data, 'multiFactor.rememberDevice') === true
        ) {
            Cookie::queue(TrustedDevices::cookieFor($user));
        }

        return $response;
    }

    private function logWrongCode(ValidationException $exception): void
    {
        $user = $this->getUserUndertakingMultiFactorAuthentication();

        if (! $user instanceof User || ! collect($exception->errors())->keys()->contains(fn (string $key): bool => str_starts_with($key, 'data.multiFactor.'))) {
            return;
        }

        activity('auth')->performedOn($user)->event('two_factor_failed')
            ->withProperties(['username' => $user->username, 'ip' => request()->ip()])
            ->log('two_factor_failed');
    }

    protected function getMultiFactorChallenge(): MultiFactorChallenge
    {
        return TrustedDeviceChallenge::make();
    }

    public function defaultMultiFactorChallengeForm(Schema $schema): Schema
    {
        return parent::defaultMultiFactorChallengeForm($schema)
            ->components(function (): array {
                $user = $this->getUserUndertakingMultiFactorAuthentication();

                if (! $user) {
                    return [];
                }

                return [
                    ...Arr::wrap($this->getMultiFactorProviderFormComponent()),
                    ...$this->getMultiFactorChallenge()->getChallengeSchemaComponents($user),
                    Checkbox::make('rememberDevice')
                        ->label(__('jejak.account.remember_device', ['days' => config('jejak.security.trusted_device_days')]))
                        ->helperText(__('jejak.account.remember_device_help', ['days' => config('jejak.security.trusted_device_days')]))
                        ->default(false),
                ];
            });
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getUsernameFormComponent(),
                $this->getPasswordFormComponent(),
            ]);
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label(__('jejak.auth.username'))
            ->required()
            ->rule('string')
            ->maxLength(50)
            ->autocomplete('username')
            ->autofocus();
    }

    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('jejak.auth.failed'),
        ]);
    }
}
