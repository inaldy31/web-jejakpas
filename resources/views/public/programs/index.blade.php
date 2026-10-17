@php
    use App\Enums\CandidateStatus;
    use App\Support\Format;
    use App\Support\HeroPhotos;
    use App\Support\StatIcons;

    $statCards = collect(['programs', 'participants', 'certified', 'assessed', 'peer_educators'])
        ->filter(fn (string $key): bool => array_key_exists($key, $summary));
    $filterFields = [
        ['name' => 'tahun', 'label' => __('jejak.programs.year'), 'options' => $years],
        ['name' => 'upt', 'label' => __('jejak.upts.model_label'), 'options' => $upts],
        ['name' => 'bidang', 'label' => __('jejak.programs.competency_field'), 'options' => $fields],
        ['name' => 'mitra', 'label' => __('jejak.partners.model_label'), 'options' => $partners],
    ];

    $heroPhotos = HeroPhotos::available('jejak.program_hero_photos');
@endphp

<x-layouts.public :title="__('jejak.nav.programs')">
    <x-page-hero :eyebrow="__('jejak.nav.programs')" :photos="$heroPhotos" :photos-label="__('jejak.hero_photos.programs_label')">
        <x-slot:title>
            {{ __('jejak.public_programs.hero_title_before') }}
            <span class="text-gold-400">{{ __('jejak.public_programs.hero_title_highlight') }}</span>
            {{ __('jejak.public_programs.hero_title_after') }}
        </x-slot:title>
        <x-slot:lead>{{ __('jejak.public_programs.hero_description') }}</x-slot:lead>
    </x-page-hero>

    <section class="relative z-10 mx-auto -mt-6 max-w-7xl px-4">
        <div @class(['grid grid-cols-2 gap-2 rounded-2xl bg-white p-3 shadow-lift ring-1 ring-slate-900/5', $statCards->count() === 5 ? 'md:grid-cols-5' : 'md:grid-cols-4'])>
            @foreach ($statCards as $key)
                <x-stat-card :label="__('jejak.stats.'.$key)" :value="$summary[$key]" :icon="StatIcons::for($key)" :class="$loop->last && $statCards->count() % 2 === 1 ? 'col-span-2 md:col-span-1' : ''" />
            @endforeach
        </div>
        <p class="mt-3 flex items-center gap-2 text-sm text-slate-600">
            <x-heroicon-o-information-circle class="h-5 w-5 shrink-0 text-slate-500" aria-hidden="true" />
            {{ __('jejak.public_programs.stats_note') }}
        </p>
        @if ($withheld)
            <x-privacy-note class="mt-2">{{ __('jejak.public.peer_educators_withheld', ['count' => $minGroupSize]) }}</x-privacy-note>
        @endif
    </section>

    <div class="mx-auto mt-10 max-w-7xl space-y-10 px-4">
        <section>
            <x-filter-form :action="route('programs.index')" :fields="$filterFields" :filters="$filters" layout="grid" />
        </section>

        <section>
            <h2 id="hasil" class="flex items-center gap-2 text-xl font-bold text-navy-900">
                <x-heroicon-o-book-open class="h-6 w-6 text-gold-700" aria-hidden="true" />
                {{ __('jejak.public_programs.list_title') }}
            </h2>
            @if ($programs->isEmpty())
                <x-empty-state class="mt-4" :title="__('jejak.public_programs.empty')">
                    {{ __('jejak.public_programs.empty_hint') }}
                    <x-slot:action>
                        <a href="{{ route('programs.index') }}" class="inline-flex h-11 items-center rounded-full bg-navy-900 px-5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-navy-800">{{ __('jejak.public.filter.reset') }}</a>
                    </x-slot:action>
                </x-empty-state>
            @else
                <x-data-table class="mt-4 rounded-2xl bg-white shadow-soft ring-1 ring-slate-900/5" :caption="__('jejak.public_programs.list_title')" data-reveal>
                    <thead class="bg-navy-900 text-left text-white">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-semibold">{{ __('jejak.programs.columns.year') }}</th>
                            <th scope="col" class="px-4 py-3 font-semibold">{{ __('jejak.programs.columns.name') }}</th>
                            <th scope="col" class="px-4 py-3 font-semibold">{{ __('jejak.programs.columns.competency_field') }}</th>
                            <th scope="col" class="px-4 py-3 font-semibold">{{ __('jejak.programs.columns.upt') }}</th>
                            <th scope="col" class="px-4 py-3 font-semibold">{{ __('jejak.partners.model_label') }}</th>
                            <th scope="col" class="px-4 py-3 text-right font-semibold">{{ __('jejak.programs.columns.total_participants') }}</th>
                            <th scope="col" class="px-4 py-3 text-right font-semibold">{{ __('jejak.programs.columns.total_certified') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($programs as $program)
                            <tr class="transition-colors duration-150 hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3 tabular-nums">{{ $program->year }}</td>
                                <th scope="row" class="px-4 py-3 text-left font-medium text-navy-900">{{ $program->name }}</th>
                                <td class="px-4 py-3">{{ $program->competency_field?->getLabel() ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $program->upt->short_name }}</td>
                                <td class="px-4 py-3">{{ $program->partner?->isPublished() ? $program->partner->name : '—' }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ Format::number($program->total_participants) }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ Format::number($program->total_certified) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-data-table>
                <div class="mt-6">{{ $programs->links() }}</div>
            @endif
        </section>

        <section>
            <h2 class="flex items-center gap-2 text-xl font-bold text-navy-900">
                <x-heroicon-o-clipboard-document-check class="h-6 w-6 text-gold-700" aria-hidden="true" />
                {{ __('jejak.public_programs.recap_title') }}
            </h2>
            <p class="mt-1 text-sm text-slate-600">{{ __('jejak.public_programs.recap_intro') }}</p>
            @if ($recap->isEmpty())
                <x-empty-state class="mt-4" icon="heroicon-o-map-pin" :title="isset($filters['upt']) ? __('jejak.public_programs.recap_not_pilot') : __('jejak.public.pilot_empty')" />
            @else
                <x-data-table class="mt-4 rounded-2xl bg-white shadow-soft ring-1 ring-slate-900/5" :caption="__('jejak.public_programs.recap_title')" data-reveal>
                    <thead class="bg-slate-100 text-left text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-semibold">{{ __('jejak.upts.model_label') }}</th>
                            <th scope="col" class="px-4 py-3 text-right font-semibold">{{ __('jejak.stats.assessed') }}</th>
                            <th scope="col" class="px-4 py-3 text-right font-semibold">{{ CandidateStatus::Siap->getLabel() }}</th>
                            <th scope="col" class="px-4 py-3 text-right font-semibold">{{ CandidateStatus::SiapDenganPenguatan->getLabel() }}</th>
                            <th scope="col" class="px-4 py-3 text-right font-semibold">{{ CandidateStatus::BelumSiap->getLabel() }}</th>
                            <th scope="col" class="px-4 py-3 text-right font-semibold">{{ CandidateStatus::PeerEducatorAktif->getLabel() }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recap as $row)
                            <tr class="transition-colors duration-150 hover:bg-slate-50">
                                <th scope="row" class="px-4 py-3 text-left font-medium text-navy-900">{{ $row['upt']->name }}</th>
                                @if ($row['assessed'] === 0)
                                    <td colspan="5" class="px-4 py-3 text-slate-500">{{ __('jejak.not_available') }}</td>
                                @elseif ($row['hide_breakdown'])
                                    <td class="px-4 py-3 text-right tabular-nums">{{ Format::number($row['assessed']) }}</td>
                                    <td colspan="4" class="px-4 py-3 text-center text-slate-500">{{ __('jejak.public_programs.recap_hidden', ['count' => $minGroupSize]) }}</td>
                                @else
                                    @foreach (['assessed', 'siap', 'penguatan', 'belum_siap', 'peer_educators'] as $column)
                                        <td class="px-4 py-3 text-right tabular-nums">{{ Format::number($row[$column]) }}</td>
                                    @endforeach
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </x-data-table>
                <p class="mt-3 text-sm text-slate-600">{{ __('jejak.public_programs.recap_note', ['count' => $minGroupSize]) }}</p>
                @if (isset($filters['tahun']))
                    <p class="mt-1 text-sm text-slate-600">{{ __('jejak.public_programs.recap_year_note') }}</p>
                @endif
            @endif
        </section>
    </div>
</x-layouts.public>
