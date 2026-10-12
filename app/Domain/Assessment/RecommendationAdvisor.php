<?php

namespace App\Domain\Assessment;

use App\Enums\Recommendation;

final class RecommendationAdvisor
{
    public function __construct(
        private readonly int $siapMinCompetency,
        private readonly int $siapMinConfidence,
        private readonly int $belumSiapMaxCompetency,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            config('jejak.recommendation.siap_min_competency'),
            config('jejak.recommendation.siap_min_confidence'),
            config('jejak.recommendation.belum_siap_max_competency'),
        );
    }

    public function suggest(AssessmentScore $score, ?string $officerObservation): ?Recommendation
    {
        if (blank($officerObservation)) {
            return null;
        }

        if ($score->competencyTotal <= $this->belumSiapMaxCompetency) {
            return Recommendation::BelumSiap;
        }

        if ($score->competencyTotal >= $this->siapMinCompetency && $score->confidenceTotal >= $this->siapMinConfidence) {
            return Recommendation::Siap;
        }

        return Recommendation::SiapDenganPenguatan;
    }
}
