<?php

namespace App\Models;

use App\Domain\Assessment\AssessmentScore;
use App\Domain\Assessment\AssessmentScorer;
use App\Domain\Assessment\FinalizationException;
use App\Domain\Assessment\RecommendationAdvisor;
use App\Enums\CompetencyField;
use App\Enums\CompetencyLevel;
use App\Enums\ConfidenceLevel;
use App\Enums\Recommendation;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\TracksAuthors;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use HasFactory, RecordsActivity, SoftDeletes, TracksAuthors;

    public const EVIDENCE_COLUMNS = ['competency_scores', 'confidence_scores', 'officer_observation'];

    protected $fillable = [
        'peer_candidate_id', 'program_id', 'competency_field', 'trainings_attended', 'certifications',
        'assessed_at', 'assessor_position', 'competency_scores', 'confidence_scores',
        'history_conclusion', 'officer_observation', 'observation_conclusion',
        'final_recommendation', 'follow_up', 'approved_by_position', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'competency_field' => CompetencyField::class,
            'assessed_at' => 'date',
            'approved_at' => 'date',
            'competency_scores' => 'array',
            'confidence_scores' => 'array',
            'competency_dimension_scores' => 'array',
            'confidence_section_scores' => 'array',
            'competency_level' => CompetencyLevel::class,
            'confidence_level' => ConfidenceLevel::class,
            'suggested_recommendation' => Recommendation::class,
            'final_recommendation' => Recommendation::class,
            'competency_total' => 'integer',
            'confidence_total' => 'integer',
        ];
    }

    public static function activityDerivedAttributes(): array
    {
        return ['upt_id'];
    }

    public static function activityRedactedAttributes(): array
    {
        return [
            'trainings_attended', 'certifications', 'history_conclusion',
            'officer_observation', 'observation_conclusion', 'follow_up',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Assessment $assessment): void {
            $assessment->upt_id = PeerCandidate::withTrashed()->whereKey($assessment->peer_candidate_id)->value('upt_id');

            if ($assessment->exists) {
                $assessment->guardFinalizedImmutability();
            }

            if (! $assessment->exists || $assessment->isDirty(self::EVIDENCE_COLUMNS)) {
                $assessment->applyScores();
            }

            $assessment->guardFinalization();
        });

        static::created(fn (Assessment $assessment) => $assessment->candidate?->syncStatusFromAssessments());
        static::updated(fn (Assessment $assessment) => $assessment->wasChanged(['final_recommendation', 'assessed_at', 'peer_candidate_id']) && $assessment->candidate?->syncStatusFromAssessments());
        static::deleted(fn (Assessment $assessment) => $assessment->candidate?->syncStatusFromAssessments());
        static::restored(fn (Assessment $assessment) => $assessment->candidate?->syncStatusFromAssessments());
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(PeerCandidate::class, 'peer_candidate_id')->withTrashed();
    }

    public function upt(): BelongsTo
    {
        return $this->belongsTo(Upt::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function applyScores(): void
    {
        $score = AssessmentScorer::fromConfig()->score($this->competency_scores ?? [], $this->confidence_scores ?? []);

        $this->competency_scores = $score->competencyScores;
        $this->confidence_scores = $score->confidenceScores;
        $this->competency_total = $score->competencyTotal;
        $this->competency_level = $score->competencyLevel;
        $this->competency_dimension_scores = $score->competencyDimensions;
        $this->confidence_total = $score->confidenceTotal;
        $this->confidence_level = $score->confidenceLevel;
        $this->confidence_section_scores = $score->confidenceSections;
        $this->suggested_recommendation = RecommendationAdvisor::fromConfig()->suggest($score, $this->officer_observation);
    }

    public function hasLockedEvidence(): bool
    {
        return $this->getOriginal('final_recommendation') !== null;
    }

    public function guardFinalizedImmutability(): void
    {
        $staysFinal = $this->final_recommendation !== null;

        if ($this->hasLockedEvidence() && $staysFinal && $this->isDirty(self::EVIDENCE_COLUMNS)) {
            throw new FinalizationException(__('jejak.assessments.finalization.locked'));
        }
    }

    public function guardFinalization(): void
    {
        if ($this->final_recommendation === null) {
            return;
        }

        $missing = array_keys(array_filter([
            __('jejak.assessments.finalization.observation') => blank($this->officer_observation),
            __('jejak.assessments.finalization.approver') => blank($this->approved_by_position),
            __('jejak.assessments.finalization.approved_at') => $this->approved_at === null,
        ]));

        if ($missing !== []) {
            throw new FinalizationException(
                __('jejak.assessments.finalization.missing', ['fields' => implode(', ', $missing)])
            );
        }
    }

    public function competencySummary(): string
    {
        return __('jejak.assessments.competency_summary', [
            'total' => (string) $this->competency_total,
            'level' => $this->competency_level->getLabel(),
        ]);
    }

    public function confidenceSummary(): string
    {
        return __('jejak.assessments.confidence_summary', [
            'total' => (string) $this->confidence_total,
            'level' => $this->confidence_level->getLabel(),
        ]);
    }

    public function competencyDimensionRows(): array
    {
        $breakdown = AssessmentScorer::fromConfig()->competencyDimensionBreakdown($this->competency_scores ?? []);

        return collect($breakdown)
            ->map(fn (array $row, string $dimension): array => ['label' => $dimension, ...$row])
            ->values()
            ->all();
    }

    public function confidenceSectionRows(): array
    {
        $breakdown = AssessmentScorer::fromConfig()->confidenceSectionBreakdown($this->confidence_scores ?? []);

        return collect($breakdown)
            ->map(fn (array $row, string $section): array => [
                'label' => $section.'. '.config("jejak.confidence_sections.{$section}"),
                ...$row,
            ])
            ->values()
            ->all();
    }

    public function weakestSummary(): string
    {
        $score = new AssessmentScore(
            competencyScores: $this->competency_scores ?? [],
            confidenceScores: $this->confidence_scores ?? [],
            competencyTotal: $this->competency_total,
            competencyLevel: $this->competency_level,
            competencyDimensions: $this->competency_dimension_scores ?? [],
            confidenceTotal: $this->confidence_total,
            confidenceLevel: $this->confidence_level,
            confidenceSections: $this->confidence_section_scores ?? [],
        );

        $dimension = $score->weakestCompetencyDimension();
        $section = $score->weakestConfidenceSection();

        return __('jejak.assessments.weakest_summary', [
            'dimension' => $dimension,
            'dimension_avg' => (string) ($this->competency_dimension_scores[$dimension] ?? ''),
            'section' => $section,
            'section_name' => (string) config("jejak.confidence_sections.{$section}"),
            'section_avg' => (string) ($this->confidence_section_scores[$section] ?? ''),
        ]);
    }
}
