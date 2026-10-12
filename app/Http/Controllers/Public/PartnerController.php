<?php

namespace App\Http\Controllers\Public;

use App\Domain\Impact\ImpactCharts;
use App\Domain\Impact\ImpactStats;
use App\Enums\CompetencyField;
use App\Enums\PartnerGroup;
use App\Enums\PksStatus;
use App\Http\Requests\PartnerFilterRequest;
use App\Models\Partner;
use App\Models\Upt;
use App\Support\FilterOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class PartnerController
{
    public function index(PartnerFilterRequest $request): View|RedirectResponse
    {
        $filters = $request->filters();

        $partners = Partner::query()->published()->with('upts')
            ->when($filters['kelompok'] ?? null, fn (Builder $q, string $v) => $q->where('group', $v))
            ->when($filters['status_pks'] ?? null, fn (Builder $q, string $v) => $q->where('pks_status', $v))
            ->when($filters['bidang'] ?? null, fn (Builder $q, string $v) => $q->withCompetency($v))
            ->when($filters['upt'] ?? null, fn (Builder $q, int|string $v) => $q->whereHas('upts', fn (Builder $u) => $u->whereKey($v)))
            ->when(filled($filters['wilayah'] ?? null), fn (Builder $q) => $q->where('province', $filters['wilayah']))
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(12, page: $request->page())
            ->appends($filters);

        if ($beyondLastPage = $request->redirectBeyondLastPage($partners)) {
            return $beyondLastPage;
        }

        $groupCounts = (new ImpactStats)->partnersByGroup();

        return view('public.partners.index', [
            'partners' => $partners,
            'filters' => $filters,
            'groups' => PartnerGroup::options(),
            'pksStatuses' => PksStatus::options(),
            'fields' => CompetencyField::options(),
            'upts' => FilterOptions::keepSelected(
                Upt::whereHas('partners', fn (Builder $q) => $q->published())->orderBy('name')->pluck('name', 'id')->all(),
                $filters['upt'] ?? null,
                FilterOptions::uptLabel(...),
            ),
            'provinces' => FilterOptions::keepSelected(
                Partner::query()->published()->whereNotNull('province')->distinct()->orderBy('province')->pluck('province', 'province')->filter(fn (string $region): bool => filled($region))->all(),
                $filters['wilayah'] ?? null,
                fn (int|string $region): string => (string) $region,
            ),
            'groupChart' => $groupCounts === [] ? null : ImpactCharts::donut($groupCounts, PartnerGroup::cases(), legend: false),
        ]);
    }

    public function show(Partner $partner): View
    {
        abort_unless($partner->isPublished(), 404);

        return view('public.partners.show', [
            'partner' => $partner->load('upts'),
            'programs' => $partner->programs()->published()->with('upt')->orderByDesc('year')->get(),
        ]);
    }
}
