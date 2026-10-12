<?php

namespace App\Domain\Assessment;

use App\Enums\CompetencyLevel;
use App\Enums\ConfidenceLevel;

final class AssessmentScorer
{
    public function __construct(
        private readonly array $competencyItems,
        private readonly array $confidenceItems,
    ) {}

    public static function fromConfig(): self
    {
        return new self(config('jejak.competency_items'), config('jejak.confidence_items'));
    }

    public function score(array $competencyScores, array $confidenceScores): AssessmentScore
    {
        $competency = $this->normalize($competencyScores, $this->competencyItems, 'Kompetensi');
        $confidence = $this->normalize($confidenceScores, $this->confidenceItems, 'Confidence');
        $competencyTotal = array_sum($competency);
        $confidenceTotal = array_sum($confidence);

        return new AssessmentScore(
            competencyScores: $competency,
            confidenceScores: $confidence,
            competencyTotal: $competencyTotal,
            competencyLevel: CompetencyLevel::fromTotal($competencyTotal),
            competencyDimensions: $this->averagesFrom($this->breakdownOf($this->groupBy($competency, $this->competencyItems, 'dimension'))),
            confidenceTotal: $confidenceTotal,
            confidenceLevel: ConfidenceLevel::fromTotal($confidenceTotal),
            confidenceSections: $this->averagesFrom($this->breakdownOf($this->groupBy($confidence, $this->confidenceItems, 'section'))),
        );
    }

    public function competencyDimensionBreakdown(array $competencyScores): array
    {
        return $this->breakdownOf($this->groupBy(
            $this->intersectValid($competencyScores, $this->competencyItems),
            $this->competencyItems,
            'dimension',
        ));
    }

    public function confidenceSectionBreakdown(array $confidenceScores): array
    {
        return $this->breakdownOf($this->groupBy(
            $this->intersectValid($confidenceScores, $this->confidenceItems),
            $this->confidenceItems,
            'section',
        ));
    }

    private function normalize(array $scores, array $items, string $instrument): array
    {
        $normalized = [];

        foreach (array_keys($items) as $number) {
            $value = $scores[$number] ?? null;

            if (! $this->isValidScore($value)) {
                throw new InvalidScoreException("{$instrument} item {$number} harus bilangan bulat 1–5.");
            }

            $normalized[$number] = (int) $value;
        }

        return $normalized;
    }

    private function intersectValid(array $scores, array $items): array
    {
        $valid = [];

        foreach (array_keys($items) as $number) {
            $value = $scores[$number] ?? null;

            if ($this->isValidScore($value)) {
                $valid[$number] = (int) $value;
            }
        }

        return $valid;
    }

    private function isValidScore(mixed $value): bool
    {
        return is_numeric($value) && (float) $value === (float) (int) $value && $value >= 1 && $value <= 5;
    }

    private function groupBy(array $scores, array $items, string $key): array
    {
        $groups = [];

        foreach ($scores as $number => $value) {
            $groups[$items[$number][$key]][] = $value;
        }

        return $groups;
    }

    private function averagesFrom(array $breakdown): array
    {
        return array_map(fn (array $row): float => $row['average'], $breakdown);
    }

    private function breakdownOf(array $groups): array
    {
        return array_map(fn (array $values): array => [
            'subtotal' => array_sum($values),
            'max' => count($values) * 5,
            'average' => round(array_sum($values) / count($values), 2),
        ], $groups);
    }
}
