<?php

namespace App\Models;

use App\Enums\CompetencyField;
use App\Enums\PartnerGroup;
use App\Enums\PksStatus;
use App\Enums\SupportType;
use App\Enums\SustainabilityStatus;
use App\Models\Concerns\HasPublication;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\TracksAuthors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Partner extends Model
{
    use HasFactory, HasPublication, RecordsActivity, SoftDeletes, TracksAuthors;

    public const MAX_COUNT = 4294967295;

    protected $hidden = ['contact', 'contact_position'];

    protected $fillable = [
        'code', 'name', 'group', 'province', 'contact_position', 'contact',
        'pks_number', 'pks_date', 'pks_valid_until', 'scope', 'pks_status',
        'competency_fields', 'support_types',
        'total_trainings', 'total_participants', 'total_certified',
        'total_peer_educators', 'total_beneficiaries', 'avg_confidence_score',
        'sustainability_status', 'best_practice_summary', 'liaison_position',
    ];

    protected function casts(): array
    {
        return [
            'group' => PartnerGroup::class,
            'pks_status' => PksStatus::class,
            'sustainability_status' => SustainabilityStatus::class,
            'competency_fields' => 'array',
            'support_types' => 'array',
            'pks_date' => 'date',
            'pks_valid_until' => 'date',
            'avg_confidence_score' => 'decimal:2',
        ];
    }

    protected function province(): Attribute
    {
        return Attribute::set(fn (?string $value): ?string => ($region = Str::squish((string) $value)) === '' ? null : $region);
    }

    protected static function booted(): void
    {
        static::creating(function (Partner $partner): void {
            if (blank($partner->code)) {
                $partner->code = self::nextCode();
            }
        });

        static::forceDeleting(function (Partner $partner): void {
            $partner->pksDocuments()->withTrashed()->get()->each(fn (PksDocument $document) => $document->forceDelete());
            $partner->programs()->withTrashed()->get()->each(fn (Program $program) => $program->update(['partner_id' => null]));
        });
    }

    public static function nextCode(): string
    {
        $max = static::withTrashed()
            ->where('code', 'like', 'M-%')
            ->pluck('code')
            ->map(fn (string $code): int => (int) substr($code, 2))
            ->max() ?? 0;

        return sprintf('M-%04d', $max + 1);
    }

    public function upts(): BelongsToMany
    {
        return $this->belongsToMany(Upt::class);
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public static function activityRedactedAttributes(): array
    {
        return ['contact'];
    }

    public function syncUpts(array $uptIds): void
    {
        $oldIds = $this->upts()->pluck('upts.id')->all();

        $changes = $this->upts()->sync($uptIds);

        if ($changes['attached'] === [] && $changes['detached'] === []) {
            return;
        }

        $names = Upt::whereIn('id', [...$oldIds, ...$uptIds])->pluck('name', 'id');

        activity('partners')
            ->performedOn($this)
            ->event('upts_updated')
            ->withProperties([
                'old' => $names->only($oldIds)->values()->all(),
                'new' => $names->only($uptIds)->values()->all(),
            ])
            ->log('upts_updated');
    }

    public function pksDocuments(): HasMany
    {
        return $this->hasMany(PksDocument::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function scopeWithCompetency(Builder $query, CompetencyField|string $field): Builder
    {
        $value = $field instanceof CompetencyField ? $field->value : $field;

        return $query->where('competency_fields', 'like', '%"'.$value.'"%');
    }

    public function competencyLabels(): array
    {
        return array_map(
            fn (string $value): string => CompetencyField::from($value)->getLabel(),
            $this->competency_fields ?? [],
        );
    }

    public function supportTypeLabels(): array
    {
        return array_map(
            fn (string $value): string => SupportType::from($value)->getLabel(),
            $this->support_types ?? [],
        );
    }

    public function hasExpiredActivePks(): bool
    {
        return $this->pks_status === PksStatus::Aktif && $this->pks_valid_until?->lt(today()) === true;
    }

    public function publicationBlockers(): array
    {
        return collect([
            'group' => $this->group,
            'pks_status' => $this->pks_status,
            'sustainability_status' => $this->sustainability_status,
            'competency_fields' => $this->competency_fields,
            'support_types' => $this->support_types,
        ])
            ->filter('blank')
            ->keys()
            ->map(fn (string $field): string => __('jejak.partners.'.$field))
            ->values()
            ->all();
    }
}
