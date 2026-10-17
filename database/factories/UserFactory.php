<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'app_authentication_secret' => (new Google2FA)->generateSecretKey(16),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function primaryAdmin(): static
    {
        return $this->state(fn (array $attributes) => ['is_primary_admin' => true]);
    }

    public function withoutTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => ['app_authentication_secret' => null, 'app_authentication_recovery_codes' => null]);
    }

    public function withRecoveryCodes(array $codes): static
    {
        return $this->state(fn (array $attributes) => [
            'app_authentication_recovery_codes' => array_map(fn (string $code): string => Hash::make($code), $codes),
        ]);
    }

    public function mustChangePassword(): static
    {
        return $this->state(fn (array $attributes) => ['must_change_password' => true]);
    }

    public function deactivated(): static
    {
        return $this->state(fn (array $attributes) => ['deactivated_at' => now()]);
    }
}
