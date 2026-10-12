<?php

namespace App\Domain\Impact;

use App\Models\BaselineSnapshot;

final class BeforeAfterComparison
{
    public static function indicators(): array
    {
        return [
            ['column' => 'total_participants', 'label' => __('jejak.charts.participants'), 'decimals' => 0, 'chart' => true, 'average' => false],
            ['column' => 'total_certified', 'label' => __('jejak.charts.certified'), 'decimals' => 0, 'chart' => true, 'average' => false],
            ['column' => 'total_peer_educators', 'label' => __('jejak.charts.peer_educators'), 'decimals' => 0, 'chart' => true, 'average' => false],
            ['column' => 'total_beneficiaries', 'label' => __('jejak.charts.beneficiaries'), 'decimals' => 0, 'chart' => true, 'average' => false],
            ['column' => 'avg_competency_score', 'label' => __('jejak.public_impact.avg_competency'), 'decimals' => 1, 'chart' => false, 'average' => true],
            ['column' => 'avg_confidence_score', 'label' => __('jejak.public_impact.avg_confidence'), 'decimals' => 1, 'chart' => false, 'average' => true],
        ];
    }

    public static function charted(): array
    {
        return array_values(array_filter(self::indicators(), fn (array $indicator): bool => $indicator['chart']));
    }

    public static function hasChartedValue(?BaselineSnapshot $baseline, ?BaselineSnapshot $post): bool
    {
        foreach (self::charted() as $indicator) {
            if ($baseline?->{$indicator['column']} !== null || $post?->{$indicator['column']} !== null) {
                return true;
            }
        }

        return false;
    }

    public static function period(?BaselineSnapshot $snapshot): ?string
    {
        $period = trim((string) $snapshot?->period_label);

        return $period === '' ? null : $period;
    }

    public static function sideLabel(string $label, ?BaselineSnapshot $snapshot): string
    {
        $period = self::period($snapshot);

        return $period === null ? $label : "{$label} ({$period})";
    }

    public static function rows(?BaselineSnapshot $baseline, ?BaselineSnapshot $post, int $minGroupSize): array
    {
        return array_map(function (array $indicator) use ($baseline, $post, $minGroupSize): array {
            $before = $baseline?->{$indicator['column']};
            $after = $post?->{$indicator['column']};

            $beforeHidden = $indicator['average'] && $before !== null && ! self::sharesAverages($baseline, $minGroupSize);
            $afterHidden = $indicator['average'] && $after !== null && ! self::sharesAverages($post, $minGroupSize);

            return [
                'label' => $indicator['label'],
                'decimals' => $indicator['decimals'],
                'before' => $beforeHidden ? null : $before,
                'after' => $afterHidden ? null : $after,
                'before_hidden' => $beforeHidden,
                'after_hidden' => $afterHidden,
                'change' => $beforeHidden || $afterHidden ? null : self::percentChange($before, $after),
            ];
        }, self::indicators());
    }

    private static function sharesAverages(?BaselineSnapshot $snapshot, int $minGroupSize): bool
    {
        return $snapshot?->total_assessed !== null && $snapshot->total_assessed >= $minGroupSize;
    }

    private static function percentChange(mixed $before, mixed $after): ?int
    {
        if (! is_numeric($before) || ! is_numeric($after) || (float) $before === 0.0) {
            return null;
        }

        return (int) round(((float) $after - (float) $before) / (float) $before * 100);
    }
}
