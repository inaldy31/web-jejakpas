<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\Assessment;
use App\Models\BaselineSnapshot;
use App\Models\Partner;
use App\Models\PeerCandidate;
use App\Models\PksDocument;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class UptAccess
{
    public static function userUptId(?User $user = null): ?int
    {
        $user ??= Auth::user();

        return $user instanceof User && $user->upt_id !== null ? (int) $user->upt_id : null;
    }

    public static function forceUpt(array $data): array
    {
        $uptId = self::userUptId();

        if ($uptId !== null) {
            $data['upt_id'] = $uptId;
        }

        return $data;
    }

    public static function restrict(Builder $query, ?User $user = null): Builder
    {
        $user ??= Auth::user();
        $uptId = self::userUptId($user);

        if ($uptId === null) {
            return $query;
        }

        $model = $query->getModel();

        return match (true) {
            self::hasUptColumn($model) => $query->where($model->qualifyColumn('upt_id'), $uptId),
            $model instanceof Partner => $query->whereHas('upts', fn (Builder $upts): Builder => $upts->whereKey($uptId)),
            $model instanceof PksDocument => $query->whereHas('partner', fn (Builder $partner): Builder => $partner->withTrashed()
                ->whereHas('upts', fn (Builder $upts): Builder => $upts->whereKey($uptId))),
            $model instanceof Activity => $query
                ->where($model->qualifyColumn('causer_type'), $user->getMorphClass())
                ->where($model->qualifyColumn('causer_id'), $user->getKey()),
            default => $query,
        };
    }

    public static function allows(User $user, Model $record): bool
    {
        $uptId = self::userUptId($user);

        if ($uptId === null) {
            return true;
        }

        return match (true) {
            self::hasUptColumn($record) => $record->getAttribute('upt_id') !== null && (int) $record->getAttribute('upt_id') === $uptId,
            $record instanceof Partner => $record->upts()->whereKey($uptId)->exists(),
            $record instanceof PksDocument => Partner::withTrashed()->whereKey($record->partner_id)
                ->whereHas('upts', fn (Builder $upts): Builder => $upts->whereKey($uptId))
                ->exists(),
            $record instanceof Activity => $record->causer_type === $user->getMorphClass() && (int) $record->causer_id === (int) $user->getKey(),
            default => true,
        };
    }

    public static function partnerUptsAfterSave(?Partner $partner, array $submittedUptIds): array
    {
        $uptId = self::userUptId();

        if ($uptId === null) {
            return $submittedUptIds;
        }

        $stored = $partner?->upts()->pluck('upts.id')->map(fn ($id): int => (int) $id)->all() ?? [];
        $others = array_values(array_diff($stored, [$uptId]));
        $keepOwn = in_array($uptId, array_map(intval(...), $submittedUptIds), true) || $others === [];

        return $keepOwn ? [...$others, $uptId] : $others;
    }

    public static function isSharedPartner(Partner $partner): bool
    {
        return $partner->upts()->count() > 1;
    }

    private static function hasUptColumn(Model $model): bool
    {
        return $model instanceof Program
            || $model instanceof PeerCandidate
            || $model instanceof Assessment
            || $model instanceof BaselineSnapshot;
    }
}
