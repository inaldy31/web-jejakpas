<?php

namespace App\Models\Concerns;

use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

trait RecordsActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->logOnly(static::activityDerivedAttributes());
    }

    public static function activityRedactedAttributes(): array
    {
        return [];
    }

    public static function activityDerivedAttributes(): array
    {
        return [];
    }

    public function beforeActivityLogged(Activity $activity, string $eventName): void
    {
        $redacted = static::activityRedactedAttributes();

        if ($redacted === [] || $activity->attribute_changes === null) {
            return;
        }

        $changes = $activity->attribute_changes->toArray();

        foreach (['attributes', 'old'] as $section) {
            foreach ($redacted as $field) {
                if (array_key_exists($field, $changes[$section] ?? [])) {
                    $changes[$section][$field] = __('jejak.activity.redacted');
                }
            }
        }

        $activity->attribute_changes = $changes;
    }
}
