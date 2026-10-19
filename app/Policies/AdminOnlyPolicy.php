<?php

namespace App\Policies;

use App\Models\User;
use App\Support\UptAccess;
use Illuminate\Database\Eloquent\Model;

abstract class AdminOnlyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Model $model): bool
    {
        return $user->isAdmin() && UptAccess::allows($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Model $model): bool
    {
        return $user->isAdmin() && UptAccess::allows($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->isAdmin() && UptAccess::allows($user, $model);
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->isAdmin() && UptAccess::allows($user, $model);
    }

    public function restoreAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->isAdmin() && UptAccess::allows($user, $model);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
