<?php

namespace App\Models;

use App\Enums\UserRole;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'username', 'password', 'role'])]
#[Hidden(['password', 'remember_token', 'app_authentication_secret', 'app_authentication_recovery_codes', 'trusted_device_key'])]
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    use HasFactory, InteractsWithAppAuthentication, InteractsWithAppAuthenticationRecovery, Notifiable;

    protected $attributes = [
        'role' => 'admin',
    ];

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            if (blank($user->trusted_device_key) || $user->isDirty(['password', 'app_authentication_secret', 'deactivated_at'])) {
                $user->trusted_device_key = Str::random(64);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_primary_admin' => 'boolean',
            'deactivated_at' => 'datetime',
            'must_change_password' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isActive(): bool
    {
        return $this->deactivated_at === null;
    }

    public function upt(): BelongsTo
    {
        return $this->belongsTo(Upt::class);
    }

    public function isUptAdmin(): bool
    {
        return $this->upt_id !== null;
    }

    public function hasTwoFactor(): bool
    {
        return filled($this->getRawOriginal('app_authentication_secret'));
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() && $this->isActive();
    }

    public function getAppAuthenticationHolderName(): string
    {
        return $this->username;
    }
}
