<?php

namespace App\Domain\Impact;

final class ImpactCharts
{
    private const NAVY = '#05213d';

    private const GOLD = '#f1bd58';

    private const GOLD_OUTLINE = '#8e620b';

    private const SLATE = '#64748b';

    private const PALETTE = ['#05213d', '#f1bd58', '#eb6834', '#1baf7a', '#008300', '#e87ba4', '#4a3aa7', '#e34948'];

    private const NEUTRAL = '#62748e';

    private const LABEL_WIDTH_BESIDE_BARS = 14;

    private const LABEL_WIDTH_UNDER_COLUMNS = 10;

    public static function beforeAfter(array $row): array
    {
        $indicators = BeforeAfterComparison::charted();

        $labels = array_map(fn (string $label): string|array => self::wrap($label, self::LABEL_WIDTH_BESIDE_BARS), array_column($indicators, 'label'));

        $values = fn (?object $snapshot): array => array_map(
            fn (array $indicator) => $snapshot?->{$indicator['column']},
            $indicators,
        );

        return self::bar($labels, [
            ['label' => BeforeAfterComparison::sideLabel(__('jejak.charts.before'), $row['baseline']), 'data' => $values($row['baseline']), 'backgroundColor' => self::SLATE],
            ['label' => BeforeAfterComparison::sideLabel(__('jejak.charts.after'), $row['post']), 'data' => $values($row['post']), 'backgroundColor' => self::NAVY],
        ], horizontal: true);
    }

    public static function funnel(array $funnel): array
    {
        $chart = self::bar(
            array_map(fn (string $label): string|array => self::wrap($label, self::LABEL_WIDTH_BESIDE_BARS), array_column($funnel, 'label')),
            [['label' => __('jejak.charts.amount'), 'data' => array_column($funnel, 'value'), 'backgroundColor' => self::NAVY]],
            horizontal: true,
        );
        $chart['options']['plugins']['legend'] = ['display' => false];

        return $chart;
    }

    public static function levels(array $counts, string $label): array
    {
        return self::bar(
            array_map(fn (string $level): string|array => self::wrap($level, self::LABEL_WIDTH_UNDER_COLUMNS), array_keys($counts)),
            [['label' => $label, 'data' => array_values($counts), 'backgroundColor' => self::GOLD, 'borderColor' => self::GOLD_OUTLINE, 'borderWidth' => 1]],
        );
    }

    public static function donut(array $counts, array $cases, bool $legend = true): array
    {
        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => array_keys($counts),
                'datasets' => [[
                    'data' => array_values($counts),
                    'backgroundColor' => self::donutColors(array_keys($counts), $cases),
                ]],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                ...($legend
                    ? ['plugins' => ['legend' => ['position' => 'bottom']]]
                    : ['cutout' => '65%', 'plugins' => ['legend' => ['display' => false]]]),
            ],
        ];
    }

    private static function donutColors(array $labels, array $cases): array
    {
        $slots = [];

        foreach ($cases as $slot => $case) {
            $slots[$case->getLabel()] = $slot;
        }

        $colors = array_map(
            fn (int|string $label): string => isset($slots[$label]) ? self::PALETTE[$slots[$label] % count(self::PALETTE)] : self::NEUTRAL,
            $labels,
        );

        return $colors === [] ? [self::PALETTE[0]] : $colors;
    }

    private static function bar(array $labels, array $datasets, bool $horizontal = false): array
    {
        $value = ['beginAtZero' => true, 'ticks' => ['precision' => 0]];
        $category = ['ticks' => ['maxRotation' => 0, 'autoSkip' => false]];

        return [
            'type' => 'bar',
            'data' => ['labels' => $labels, 'datasets' => $datasets],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'locale' => 'id-ID',
                'plugins' => ['legend' => ['position' => 'bottom']],
                ...($horizontal
                    ? ['indexAxis' => 'y', 'scales' => ['x' => $value, 'y' => $category]]
                    : ['scales' => ['x' => $category, 'y' => $value]]),
            ],
        ];
    }

    private static function wrap(string $label, int $width): string|array
    {
        $lines = explode("\n", wordwrap($label, $width, "\n"));

        return count($lines) === 1 ? $label : $lines;
    }
}
