<?php

namespace App\Policies;

use App\Models\Partner;
use App\Models\User;
use App\Support\UptAccess;
use Illuminate\Database\Eloquent\Model;

class PartnerPolicy extends AdminOnlyPolicy
{
    public function delete(User $user, Model $model): bool
    {
        return parent::delete($user, $model) && ! $this->isSharedForUptAdmin($user, $model);
    }

    public function restore(User $user, Model $model): bool
    {
        return parent::restore($user, $model) && ! $this->isSharedForUptAdmin($user, $model);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return parent::forceDelete($user, $model) && ! $this->isSharedForUptAdmin($user, $model);
    }

    private function isSharedForUptAdmin(User $user, Model $model): bool
    {
        return $user->isUptAdmin() && $model instanceof Partner && (UptAccess::isSharedPartner($model)
            || $model->programs()->withTrashed()->where('upt_id', '!=', UptAccess::userUptId($user))->exists());
    }
}
