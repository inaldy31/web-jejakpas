<?php

namespace App\Models;

use App\Enums\CandidateStatus;
use App\Enums\CompetencyField;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\TracksAuthors;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PeerCandidate extends Model
{
    use HasFactory, RecordsActivity, SoftDeletes, TracksAuthors;

    public const DESIGNATED_STATUSES = [CandidateStatus::PeerEducatorAktif, CandidateStatus::Nonaktif];

    protected $fillable = [
        'code', 'upt_id', 'label', 'competency_field', 'program_id',
        'current_status', 'designated_at', 'approved_by_position', 'notes',
    ];

    private static array $forceDeletingIds = [];

    protected function casts(): array
    {
        return [
            'competency_field' => CompetencyField::class,
            'current_status' => CandidateStatus::class,
            'designated_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PeerCandidate $candidate): void {
            $candidate->current_status ??= CandidateStatus::Calon;

            if (blank($candidate->code)) {
                $candidate->code = self::nextCode(Upt::findOrFail($candidate->upt_id));
            }
        });

        static::forceDeleting(function (PeerCandidate $candidate): void {
            static::$forceDeletingIds[$candidate->id] = true;

            try {
                $candidate->assessments()->withTrashed()->get()
                    ->each(fn (Assessment $assessment) => $assessment->forceDelete());
            } finally {
                unset(static::$forceDeletingIds[$candidate->id]);
            }
        });

        static::updated(function (PeerCandidate $candidate): void {
            if (! $candidate->wasChanged('upt_id')) {
                return;
            }

            $candidate->assessments()->withTrashed()->get()
                ->each(fn (Assessment $assessment) => $assessment->save());
        });
    }

    public static function activityRedactedAttributes(): array
    {
        return ['label', 'notes'];
    }

    public static function nextCode(Upt $upt): string
    {
        $prefix = 'PE-'.Str::upper(Str::substr(Str::slug($upt->short_name ?: $upt->name, ''), 0, 3)).'-';

        $max = static::withTrashed()
            ->where('code', 'like', $prefix.'%')
            ->pluck('code')
            ->map(fn (string $code): int => (int) Str::after($code, $prefix))
            ->max() ?? 0;

        return $prefix.str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    public function upt(): BelongsTo
    {
        return $this->belongsTo(Upt::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function latestFinalAssessment(): HasOne
    {
        return $this->hasOne(Assessment::class)->ofMany(
            ['assessed_at' => 'max', 'id' => 'max'],
            fn (Builder $query) => $query->whereNotNull('final_recommendation'),
        );
    }

    public function syncStatusFromAssessments(): void
    {
        if (isset(static::$forceDeletingIds[$this->id])) {
            return;
        }

        $current = $this->freshStatusRow();

        if ($current !== null && in_array($current->current_status, self::DESIGNATED_STATUSES, true)) {
            return;
        }

        $latest = $this->latestFinalAssessment()->first();

        $this->update([
            'current_status' => $latest?->final_recommendation->candidateStatus() ?? CandidateStatus::Calon,
        ]);
    }

    public function activateAsPeerEducator(string $approvedByPosition, CarbonInterface $designatedAt): void
    {
        if ($this->freshStatusOrFail() !== CandidateStatus::Siap) {
            throw new DomainException(__('jejak.candidates.activation.not_ready'));
        }

        $this->update([
            'current_status' => CandidateStatus::PeerEducatorAktif,
            'designated_at' => $designatedAt,
            'approved_by_position' => $approvedByPosition,
        ]);
    }

    public function deactivate(string $reason): void
    {
        if ($this->freshStatusOrFail() !== CandidateStatus::PeerEducatorAktif) {
            throw new DomainException(__('jejak.candidates.deactivation.not_active'));
        }

        $this->update(['current_status' => CandidateStatus::Nonaktif]);

        activity('peer_educator')->performedOn($this)->event('deactivated')
            ->withProperties(['reason' => $reason])->log('deactivated');
    }

    public function reactivate(): void
    {
        if ($this->freshStatusOrFail() !== CandidateStatus::Nonaktif) {
            throw new DomainException(__('jejak.candidates.reactivation.not_inactive'));
        }

        $this->update(['current_status' => CandidateStatus::PeerEducatorAktif]);

        activity('peer_educator')->performedOn($this)->event('reactivated')->log('reactivated');
    }

    public function correctDesignation(string $approvedByPosition, CarbonInterface $designatedAt): void
    {
        $status = $this->freshStatusOrFail();

        if (! in_array($status, self::DESIGNATED_STATUSES, true)) {
            throw new DomainException(__('jejak.candidates.correction.wrong_status'));
        }

        $this->update([
            'approved_by_position' => $approvedByPosition,
            'designated_at' => $designatedAt,
        ]);
    }

    private function freshStatusRow(): ?object
    {
        return static::withTrashed()->whereKey($this->id)->select(['current_status', 'deleted_at'])->first();
    }

    private function freshStatusOrFail(): CandidateStatus
    {
        $current = $this->freshStatusRow();

        if ($current === null) {
            throw new DomainException(__('jejak.candidates.missing'));
        }

        if ($current->deleted_at !== null) {
            throw new DomainException(__('jejak.candidates.archived'));
        }

        return $current->current_status;
    }
}
