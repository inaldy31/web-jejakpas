<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UptPolicy extends AdminOnlyPolicy
{
    public function create(User $user): bool
    {
        return $this->isCentralAdmin($user);
    }

    public function update(User $user, Model $model): bool
    {
        return $this->isCentralAdmin($user);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->isCentralAdmin($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->isCentralAdmin($user);
    }

    public function restore(User $user, Model $model): bool
    {
        return $this->isCentralAdmin($user);
    }

    public function restoreAny(User $user): bool
    {
        return $this->isCentralAdmin($user);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $this->isCentralAdmin($user);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->isCentralAdmin($user);
    }

    private function isCentralAdmin(User $user): bool
    {
        return $user->isAdmin() && ! $user->isUptAdmin();
    }
}
