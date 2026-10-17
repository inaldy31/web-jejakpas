<?php

namespace App\Http\Controllers\Public;

use App\Domain\Impact\BeforeAfterComparison;
use App\Domain\Impact\ImpactCharts;
use App\Domain\Impact\ImpactStats;
use App\Enums\CompetencyField;
use App\Enums\PartnerGroup;
use App\Enums\PksStatus;
use App\Enums\SustainabilityStatus;
use App\Http\Requests\ImpactFilterRequest;
use App\Models\Upt;
use App\Support\FilterOptions;
use Illuminate\View\View;

final class ImpactController
{
    public function __invoke(ImpactFilterRequest $request): View
    {
        $filters = $request->filters();

        $stats = new ImpactStats(
            isset($filters['tahun']) ? (int) $filters['tahun'] : null,
            isset($filters['upt']) ? (int) $filters['upt'] : null,
        );

        $minGroupSize = ImpactStats::minGroupSize();

        $withheld = $stats->withholdsResults();

        $funnel = array_map(
            fn (array $stage): array => $withheld && in_array($stage['key'], ImpactStats::WITHHELD, true)
                ? [...$stage, 'value' => null, 'hidden' => true]
                : [...$stage, 'hidden' => false],
            $stats->funnel(),
        );

        $distribution = $withheld ? null : $stats->assessmentDistribution();

        return view('public.impact', [
            'filters' => $filters,
            'summary' => $stats->publicSummary(),
            'withheld' => $withheld,
            'minGroupSize' => $minGroupSize,
            'funnel' => $funnel,
            'funnelChart' => collect($funnel)->contains(fn (array $stage): bool => $stage['value'] !== null) ? ImpactCharts::funnel($funnel) : null,
            'beforeAfter' => $stats->beforeAfter()->map(fn (array $row): array => [
                'upt' => $row['upt'],
                'has_baseline' => $row['baseline'] !== null,
                'has_post' => $row['post'] !== null,
                'before_period' => BeforeAfterComparison::period($row['baseline']),
                'after_period' => BeforeAfterComparison::period($row['post']),
                'chart' => BeforeAfterComparison::hasChartedValue($row['baseline'], $row['post']) ? ImpactCharts::beforeAfter($row) : null,
                'rows' => BeforeAfterComparison::rows($row['baseline'], $row['post'], $minGroupSize),
            ]),
            'levels' => $distribution === null ? [] : [
                $this->levels(__('jejak.charts.competency'), $distribution['competency']),
                $this->levels(__('jejak.charts.confidence'), $distribution['confidence']),
            ],
            'distributions' => [
                $this->distribution(__('jejak.public_impact.group_title'), $stats->partnersByGroup(), PartnerGroup::cases(), __('jejak.public_impact.unit_partners')),
                $this->distribution(__('jejak.public_impact.competency_title'), $stats->programsByCompetency(), CompetencyField::cases(), __('jejak.public_impact.unit_programs')),
                $this->distribution(__('jejak.public_impact.pks_title'), $stats->partnersByPksStatus(), PksStatus::cases(), __('jejak.public_impact.unit_partners')),
                $this->distribution(__('jejak.public_impact.sustainability_title'), $stats->partnersBySustainability(), SustainabilityStatus::cases(), __('jejak.public_impact.unit_partners')),
            ],
            'years' => FilterOptions::years($filters['tahun'] ?? null),
            'upts' => Upt::where('is_pilot', true)->orderBy('name')->pluck('name', 'id')->all(),
        ]);
    }

    private function levels(string $label, array $counts): array
    {
        return ['label' => $label, 'counts' => $counts, 'chart' => ImpactCharts::levels($counts, $label)];
    }

    private function distribution(string $title, array $counts, array $cases, string $unit): array
    {
        return [
            'title' => $title,
            'unit' => $unit,
            'total' => array_sum($counts),
            'counts' => $counts,
            'chart' => $counts === [] ? null : ImpactCharts::donut($counts, $cases, legend: false),
        ];
    }
}
