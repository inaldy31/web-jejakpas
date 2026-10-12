<?php

namespace App\Models\Concerns;

use App\Enums\PublicationStatus;
use App\Models\Exceptions\PublicationBlocked;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasPublication
{
    public function initializeHasPublication(): void
    {
        $this->mergeCasts([
            'status' => PublicationStatus::class,
            'published_at' => 'datetime',
        ]);
    }

    public static function bootHasPublication(): void
    {
        static::creating(function (Model $model): void {
            $model->status ??= PublicationStatus::Draft;
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('status'), PublicationStatus::Published->value);
    }

    public function isPublished(): bool
    {
        return $this->status === PublicationStatus::Published;
    }

    public function publicationBlockers(): array
    {
        return [];
    }

    public function publish(): void
    {
        $blockers = $this->publicationBlockers();

        if ($blockers !== []) {
            throw new PublicationBlocked($blockers);
        }

        $this->forceFill([
            'status' => PublicationStatus::Published,
            'published_at' => $this->published_at ?? now(),
        ])->save();

        $this->logPublication('published');
    }

    public function unpublish(): void
    {
        $this->forceFill(['status' => PublicationStatus::Draft])->save();

        $this->logPublication('unpublished');
    }

    private function logPublication(string $event): void
    {
        activity('publication')->performedOn($this)->event($event)->log($event);
    }
}
