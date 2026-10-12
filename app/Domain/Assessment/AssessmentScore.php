<?php

namespace App\Domain\Assessment;

use App\Enums\CompetencyLevel;
use App\Enums\ConfidenceLevel;

final readonly class AssessmentScore
{
    public function __construct(
        public array $competencyScores,
        public array $confidenceScores,
        public int $competencyTotal,
        public CompetencyLevel $competencyLevel,
        public array $competencyDimensions,
        public int $confidenceTotal,
        public ConfidenceLevel $confidenceLevel,
        public array $confidenceSections,
    ) {}

    public function weakestCompetencyDimension(): string
    {
        return $this->lowestAverageKey($this->competencyDimensions);
    }

    public function weakestConfidenceSection(): string
    {
        return $this->lowestAverageKey($this->confidenceSections);
    }

    private function lowestAverageKey(array $averages): string
    {
        return (string) array_search(min($averages), $averages, true);
    }
}
