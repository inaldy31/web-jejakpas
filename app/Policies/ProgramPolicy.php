<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\PeerCandidate;
use App\Models\Program;
use App\Models\User;
use App\Support\UptAccess;
use Illuminate\Database\Eloquent\Model;

class ProgramPolicy extends AdminOnlyPolicy
{
    public function delete(User $user, Model $model): bool
    {
        return parent::delete($user, $model) && ! $this->isUsedByOtherUpt($user, $model);
    }

    public function restore(User $user, Model $model): bool
    {
        return parent::restore($user, $model) && ! $this->isUsedByOtherUpt($user, $model);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return parent::forceDelete($user, $model) && ! $this->isUsedByOtherUpt($user, $model);
    }

    private function isUsedByOtherUpt(User $user, Model $model): bool
    {
        $uptId = UptAccess::userUptId($user);

        if ($uptId === null || ! $model instanceof Program) {
            return false;
        }

        return PeerCandidate::withTrashed()->where('program_id', $model->getKey())->where('upt_id', '!=', $uptId)->exists()
            || Assessment::withTrashed()->where('program_id', $model->getKey())->where('upt_id', '!=', $uptId)->exists();
    }
}
