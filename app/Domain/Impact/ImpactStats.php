<?php

namespace App\Domain\Impact;

use App\Enums\CandidateStatus;
use App\Enums\CompetencyField;
use App\Enums\CompetencyLevel;
use App\Enums\ConfidenceLevel;
use App\Enums\PartnerGroup;
use App\Enums\PksStatus;
use App\Enums\Recommendation;
use App\Enums\SnapshotPhase;
use App\Enums\SustainabilityStatus;
use App\Models\Assessment;
use App\Models\BaselineSnapshot;
use App\Models\Partner;
use App\Models\PeerCandidate;
use App\Models\Program;
use App\Models\Upt;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class ImpactStats
{
    private const FUNNEL = ['participants', 'certified', 'assessed', 'ready', 'peer_educators', 'beneficiaries'];

    private ?array $summary = null;

    private array $latestSnapshots = [];

    private ?Collection $latestFinal = null;

    public function __construct(
        public readonly ?int $year = null,
        public readonly ?int $uptId = null,
    ) {}

    public function summary(): array
    {
        return $this->summary ??= $this->buildSummary();
    }

    private function buildSummary(): array
    {
        $hasAssessments = $this->assessedCandidates()->exists();

        return [
            'partners' => $this->partners()->count(),
            'pilot_upts' => $this->pilotUpts()->count(),
            'programs' => $this->programs()->count(),
            'participants' => $this->sumOrNull($this->programs(), 'total_participants'),
            'certified' => $this->sumOrNull($this->programs(), 'total_certified'),
            'assessed' => $hasAssessments ? $this->assessedCandidates()->count() : null,
            'ready' => $hasAssessments ? $this->readyCount() : null,
            'peer_educators' => $hasAssessments
                ? $this->assessedCandidates()->where('current_status', CandidateStatus::PeerEducatorAktif->value)->count()
                : null,
            'beneficiaries' => $this->beneficiaries(),
        ];
    }

    public function funnel(): array
    {
        $summary = $this->summary();

        return array_map(
            fn (string $key): array => ['key' => $key, 'label' => __("jejak.stats.{$key}"), 'value' => $summary[$key]],
            self::FUNNEL,
        );
    }

    public function partnersByGroup(): array
    {
        return $this->countByCase($this->partners()->pluck('group'), PartnerGroup::cases());
    }

    public function partnersByPksStatus(): array
    {
        return $this->countByCase($this->partners()->pluck('pks_status'), PksStatus::cases());
    }

    public function partnersBySustainability(): array
    {
        return $this->countByCase($this->partners()->pluck('sustainability_status'), SustainabilityStatus::cases());
    }

    public function programsByCompetency(): array
    {
        return $this->countByCase($this->programs()->pluck('competency_field'), CompetencyField::cases());
    }

    public function beforeAfter(): Collection
    {
        return $this->pilotUpts()->orderBy('short_name')->get()->map(function (Upt $upt): array {
            $baseline = $this->latestSnapshot($upt, SnapshotPhase::Baseline);
            $post = $this->latestSnapshot($upt, SnapshotPhase::Post);

            if ($baseline !== null && $post !== null && $post->recorded_at->lt($baseline->recorded_at)) {
                $post = null;
            }

            return ['upt' => $upt, 'baseline' => $baseline, 'post' => $post];
        });
    }

    public function assessmentDistribution(): ?array
    {
        $rows = $this->latestFinalAssessments();

        if ($rows->isEmpty()) {
            return null;
        }

        return [
            'competency' => collect(CompetencyLevel::cases())
                ->mapWithKeys(fn (CompetencyLevel $level): array => [$level->getLabel() => $rows->where('competency_level', $level)->count()])
                ->all(),
            'confidence' => collect(ConfidenceLevel::cases())
                ->mapWithKeys(fn (ConfidenceLevel $level): array => [$level->getLabel() => $rows->where('confidence_level', $level)->count()])
                ->all(),
        ];
    }

    public function assessmentRecapByUpt(): Collection
    {
        return $this->pilotUpts()->orderBy('short_name')->get()->map(function (Upt $upt): array {
            $candidates = PeerCandidate::query()
                ->where('upt_id', $upt->id)
                ->whereHas('assessments', fn (Builder $query) => $this->finalAssessments($query))
                ->get(['id', 'current_status']);
            $statuses = $candidates->pluck('current_status');

            $results = $this->year === null
                ? $statuses
                : $this->latestFinalAssessments()
                    ->whereIn('peer_candidate_id', $candidates->modelKeys())
                    ->map(fn (Assessment $assessment): CandidateStatus => $assessment->final_recommendation->candidateStatus());

            return [
                'upt' => $upt,
                'assessed' => $candidates->count(),
                'siap' => $results->filter(fn (CandidateStatus $s): bool => $s === CandidateStatus::Siap)->count(),
                'penguatan' => $results->filter(fn (CandidateStatus $s): bool => $s === CandidateStatus::SiapDenganPenguatan)->count(),
                'belum_siap' => $results->filter(fn (CandidateStatus $s): bool => $s === CandidateStatus::BelumSiap)->count(),
                'peer_educators' => $statuses->filter(fn (CandidateStatus $s): bool => $s === CandidateStatus::PeerEducatorAktif)->count(),
            ];
        });
    }

    private function countByCase(Collection $values, array $cases): array
    {
        $tally = $values->countBy(fn (?BackedEnum $value): string => $value?->value ?? '');
        $counts = [];

        foreach ($cases as $case) {
            if (($tally[$case->value] ?? 0) > 0) {
                $counts[$case->getLabel()] = $tally[$case->value];
            }
        }

        if (($tally[''] ?? 0) > 0) {
            $counts[__('jejak.not_available')] = $tally[''];
        }

        return $counts;
    }

    private function partners(): Builder
    {
        return Partner::query()->published()
            ->when($this->uptId, fn (Builder $query) => $query->whereHas('upts', fn (Builder $upts) => $upts->whereKey($this->uptId)));
    }

    private function programs(): Builder
    {
        return Program::query()->published()
            ->when($this->year, fn (Builder $query) => $query->where('year', $this->year))
            ->when($this->uptId, fn (Builder $query) => $query->where('upt_id', $this->uptId));
    }

    private function pilotUpts(): Builder
    {
        return Upt::query()->where('is_pilot', true)
            ->when($this->uptId, fn (Builder $query) => $query->whereKey($this->uptId));
    }

    private function candidates(): Builder
    {
        return PeerCandidate::query()
            ->when($this->uptId, fn (Builder $query) => $query->where('upt_id', $this->uptId));
    }

    private function assessedCandidates(): Builder
    {
        return $this->candidates()->whereHas('assessments', fn (Builder $query) => $this->finalAssessments($query));
    }

    private function finalAssessments(Builder $query): Builder
    {
        return $query->whereNotNull('final_recommendation')
            ->when($this->year, fn (Builder $q) => $q->whereYear('assessed_at', $this->year));
    }

    private function readyCount(): int
    {
        if ($this->year === null) {
            return $this->assessedCandidates()->whereIn('current_status', [
                CandidateStatus::Siap->value, CandidateStatus::PeerEducatorAktif->value, CandidateStatus::Nonaktif->value,
            ])->count();
        }

        return $this->latestFinalAssessments()
            ->filter(fn (Assessment $assessment): bool => $assessment->final_recommendation === Recommendation::Siap)
            ->count();
    }

    private function latestFinalAssessments(): Collection
    {
        return $this->latestFinal ??= $this->finalAssessments(Assessment::query())
            ->whereIn('peer_candidate_id', $this->candidates()->select('id'))
            ->orderByDesc('assessed_at')
            ->orderByDesc('id')
            ->get(['peer_candidate_id', 'competency_level', 'confidence_level', 'final_recommendation'])
            ->unique('peer_candidate_id');
    }

    private function latestSnapshot(Upt $upt, SnapshotPhase $phase): ?BaselineSnapshot
    {
        $key = $upt->id.'|'.$phase->value;

        if (! array_key_exists($key, $this->latestSnapshots)) {
            $this->latestSnapshots[$key] = BaselineSnapshot::query()->published()
                ->where('upt_id', $upt->id)
                ->where('phase', $phase->value)
                ->orderByDesc('recorded_at')
                ->orderByDesc('id')
                ->first();
        }

        return $this->latestSnapshots[$key];
    }

    private function beneficiaries(): ?int
    {
        $values = $this->pilotUpts()->get()
            ->map(fn (Upt $upt): ?int => $this->latestSnapshot($upt, SnapshotPhase::Post)?->total_beneficiaries)
            ->filter(fn (?int $value): bool => $value !== null);

        return $values->isEmpty() ? null : $values->sum();
    }

    private function sumOrNull(Builder $query, string $column): ?int
    {
        $query->whereNotNull($column);

        return $query->exists() ? (int) $query->sum($column) : null;
    }
}
