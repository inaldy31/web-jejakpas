<?php

namespace App\Http\Controllers\Public;

use App\Domain\Impact\ImpactStats;
use App\Enums\CompetencyField;
use App\Http\Requests\ProgramFilterRequest;
use App\Models\Partner;
use App\Models\Program;
use App\Models\Upt;
use App\Support\FilterOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\View;

final class ProgramController
{
    public function __invoke(ProgramFilterRequest $request): View|RedirectResponse
    {
        $filters = $request->filters();

        $programs = Program::query()->published()->with(['upt', 'partner'])
            ->when($filters['tahun'] ?? null, fn (Builder $q, int|string $v) => $q->where('year', $v))
            ->when($filters['upt'] ?? null, fn (Builder $q, int|string $v) => $q->where('upt_id', $v))
            ->when($filters['bidang'] ?? null, fn (Builder $q, string $v) => $q->where('competency_field', $v))
            ->when($filters['mitra'] ?? null, fn (Builder $q, int|string $v) => $q->whereHas('partner', fn (Builder $p) => $p->published()->whereKey($v)))
            ->orderByDesc('year')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(20, page: $request->page())
            ->appends($filters);

        if ($beyondLastPage = $request->redirectBeyondLastPage($programs)) {
            return $beyondLastPage;
        }

        $stats = new ImpactStats(
            isset($filters['tahun']) ? (int) $filters['tahun'] : null,
            isset($filters['upt']) ? (int) $filters['upt'] : null,
        );
        $minGroupSize = (int) config('jejak.privacy.min_group_size');

        return view('public.programs.index', [
            'programs' => $programs,
            'filters' => $filters,
            'summary' => Arr::except($stats->summary(), 'ready'),
            'recap' => $this->recapRows($stats, $minGroupSize),
            'minGroupSize' => $minGroupSize,
            'years' => FilterOptions::years($filters['tahun'] ?? null),
            'upts' => FilterOptions::keepSelected(
                Upt::whereHas('programs', fn (Builder $q) => $q->published())->orderBy('name')->pluck('name', 'id')->all(),
                $filters['upt'] ?? null,
                FilterOptions::uptLabel(...),
            ),
            'fields' => CompetencyField::options(),
            'partners' => FilterOptions::keepSelected(
                Partner::query()->published()
                    ->whereHas('programs', fn (Builder $q) => $q->published())
                    ->orderBy('name')->pluck('name', 'id')->all(),
                $filters['mitra'] ?? null,
                FilterOptions::partnerLabel(...),
            ),
        ]);
    }

    private function recapRows(ImpactStats $stats, int $minGroupSize): Collection
    {
        return $stats->assessmentRecapByUpt()->map(function (array $row) use ($minGroupSize): array {
            $hideBreakdown = $row['assessed'] > 0 && $row['assessed'] < $minGroupSize;

            return [
                ...($hideBreakdown ? Arr::except($row, ['siap', 'penguatan', 'belum_siap']) : $row),
                'hide_breakdown' => $hideBreakdown,
            ];
        });
    }
}
