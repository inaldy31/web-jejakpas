<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isPrimary($user);
    }

    public function view(User $user, User $model): bool
    {
        return $this->isPrimary($user);
    }

    public function create(User $user): bool
    {
        return $this->isPrimary($user);
    }

    public function update(User $user, User $model): bool
    {
        return $this->isPrimary($user);
    }

    public function deactivate(User $user, User $model): bool
    {
        return $this->isPrimary($user) && ! $model->is($user) && ! $model->is_primary_admin && $model->isActive();
    }

    public function reactivate(User $user, User $model): bool
    {
        return $this->isPrimary($user) && ! $model->isActive();
    }

    public function resetTwoFactor(User $user, User $model): bool
    {
        return $this->isPrimary($user) && ! $model->is($user) && $model->hasTwoFactor();
    }

    public function resetPassword(User $user, User $model): bool
    {
        return $this->isPrimary($user) && ! $model->is($user);
    }

    public function delete(User $user, User $model): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, User $model): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    private function isPrimary(User $user): bool
    {
        return $user->isAdmin() && $user->is_primary_admin && $user->isActive();
    }
}
