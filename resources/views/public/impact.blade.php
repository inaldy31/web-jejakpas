@php
    use App\Support\Format;
    use App\Support\HeroPhotos;
    use App\Support\StatIcons;

    $cards = collect(['pilot_upts', 'partners', 'programs', 'participants', 'certified', 'assessed', 'ready', 'peer_educators', 'beneficiaries'])
        ->reject(fn (string $key): bool => $hideReadiness && $key === 'ready');

    $filterFields = [
        ['name' => 'tahun', 'label' => __('jejak.programs.year'), 'options' => $years],
        ['name' => 'upt', 'label' => __('jejak.upts.model_label'), 'options' => $upts, 'all' => __('jejak.public_impact.filter_all_upts')],
    ];

    $heroPhotos = HeroPhotos::available('jejak.impact_hero_photos');
@endphp

<x-layouts.public :title="__('jejak.nav.impact')">
    <x-page-hero :eyebrow="__('jejak.nav.impact')" :photos="$heroPhotos" :photos-label="__('jejak.hero_photos.impact_label')">
        <x-slot:title>
            {{ __('jejak.public_impact.hero_title_before') }}
            <span class="text-gold-400">{{ __('jejak.public_impact.hero_title_highlight') }}</span>
        </x-slot:title>
        <x-slot:lead>{{ __('jejak.public_impact.hero_description') }}</x-slot:lead>
    </x-page-hero>

    <section class="relative z-10 mx-auto -mt-6 max-w-7xl px-4">
        <h2 id="hasil" class="sr-only">{{ __('jejak.public_impact.summary_title') }}</h2>
        <div class="grid grid-cols-2 gap-2 rounded-2xl bg-white p-3 shadow-lift ring-1 ring-slate-900/5 md:grid-cols-3 xl:grid-cols-5">
            @foreach ($cards as $key)
                <x-stat-card :label="__('jejak.stats.'.$key)" :value="$summary[$key]" :icon="StatIcons::for($key)" :class="$loop->last && $cards->count() % 2 === 1 ? 'col-span-2 md:col-span-1' : ''" />
            @endforeach
        </div>
        @if ($hideReadiness)
            <p class="mt-3 flex items-start gap-2 text-sm text-slate-600">
                <x-heroicon-o-lock-closed class="mt-0.5 h-5 w-5 shrink-0 text-slate-500" aria-hidden="true" />
                {{ __('jejak.public_impact.privacy_note', ['count' => $minGroupSize]) }}
            </p>
        @endif
    </section>

    <div class="mx-auto mt-10 max-w-7xl space-y-10 px-4">
        <section>
            <x-filter-form :action="route('impact')" :fields="$filterFields" :filters="$filters" layout="inline">
                {{ __('jejak.public_impact.filter_note') }}
            </x-filter-form>
        </section>

        <section>
            <h2 class="flex items-center gap-2 text-xl font-bold text-navy-900">
                <x-heroicon-o-arrows-right-left class="h-6 w-6 shrink-0 text-gold-700" aria-hidden="true" />
                {{ __('jejak.public_impact.before_after_title') }}
            </h2>
            <p class="mt-1 text-sm text-slate-600">{{ __('jejak.public_impact.before_after_note') }}</p>
            @if ($beforeAfter->isEmpty())
                <x-empty-state class="mt-4" icon="heroicon-o-map-pin" :title="__('jejak.public.pilot_empty')" />
            @else
                <div class="mt-4 grid gap-6 md:grid-cols-2 xl:grid-cols-3" data-reveal>
                    @foreach ($beforeAfter as $row)
                        @php $caption = __('jejak.public_impact.before_after_chart', ['upt' => $row['upt']->name]); @endphp
                        <div class="min-w-0 rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-900/5">
                            <h3 class="font-bold text-navy-900">{{ $row['upt']->name }}</h3>
                            @unless ($row['has_baseline'])
                                <p class="mt-2 text-sm text-slate-600">{{ __('jejak.public_impact.baseline_missing') }}</p>
                            @endunless
                            @unless ($row['has_post'])
                                <p class="mt-2"><span class="rounded-full bg-navy-50 px-2.5 py-0.5 text-xs font-medium text-navy-900">{{ __('jejak.public_impact.post_missing') }}</span></p>
                            @endunless
                            @if ($row['chart'])
                                <x-chart :config="$row['chart']" class="mt-4 h-56" role="img" :aria-label="$caption" />
                            @endif
                            @if ($row['has_baseline'] || $row['has_post'])
                                <x-data-table class="mt-4" :caption="$caption">
                                    <thead>
                                        <tr class="text-xs text-slate-600">
                                            <th scope="col" class="pb-2 text-left font-medium">{{ __('jejak.public_impact.measure') }}</th>
                                            <th scope="col" class="whitespace-nowrap pb-2 pl-3 text-right font-medium">
                                                {{ __('jejak.charts.before') }}
                                                @if ($row['before_period'])
                                                    <span class="block whitespace-normal font-normal">({{ $row['before_period'] }})</span>
                                                @endif
                                            </th>
                                            <th scope="col" class="whitespace-nowrap pb-2 pl-3 text-right font-medium">
                                                {{ __('jejak.charts.after') }}
                                                @if ($row['after_period'])
                                                    <span class="block whitespace-normal font-normal">({{ $row['after_period'] }})</span>
                                                @endif
                                            </th>
                                            <th scope="col" class="whitespace-nowrap pb-2 pl-3 text-right font-medium">{{ __('jejak.public_impact.change') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($row['rows'] as $line)
                                            <tr>
                                                <th scope="row" class="py-2 pr-2 text-left font-normal text-slate-600">{{ $line['label'] }}</th>
                                                @foreach (['before', 'after'] as $side)
                                                    @php $value = $line[$side]; @endphp
                                                    <td @class(['py-2 pl-3 text-right tabular-nums', 'whitespace-nowrap font-semibold text-navy-900' => $value !== null, 'text-xs text-slate-500' => $value === null])>{{ $line[$side.'_hidden'] ? __('jejak.public_impact.hidden') : Format::number($value, $line['decimals']) }}</td>
                                                @endforeach
                                                @php $change = $line['change']; @endphp
                                                <td class="whitespace-nowrap py-2 pl-3 text-right">
                                                    @if ($change === null)
                                                        <span class="text-xs text-slate-500">—</span>
                                                    @else
                                                        <span @class(['inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums', 'bg-emerald-50 text-emerald-800' => $change > 0, 'bg-slate-100 text-slate-700' => $change <= 0])>
                                                            @if ($change > 0)
                                                                <x-heroicon-o-arrow-trending-up class="h-3.5 w-3.5 shrink-0" aria-hidden="true" />
                                                            @elseif ($change < 0)
                                                                <x-heroicon-o-arrow-trending-down class="h-3.5 w-3.5 shrink-0" aria-hidden="true" />
                                                            @endif
                                                            {{ $change > 0 ? '+' : ($change < 0 ? "\u{2212}" : '') }}{{ Format::number(abs($change)) }}%
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </x-data-table>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="grid gap-6 lg:grid-cols-2" data-reveal>
            <div class="min-w-0 rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-900/5">
                <h2 class="flex items-center gap-2 text-lg font-bold text-navy-900">
                    <x-heroicon-o-arrow-trending-up class="h-5 w-5 shrink-0 text-gold-700" aria-hidden="true" />
                    {{ __('jejak.public_impact.funnel_title') }}
                </h2>
                @if ($funnelChart)
                    <x-chart :config="$funnelChart" class="mt-4 h-72" role="img" :aria-label="__('jejak.public_impact.funnel_title')" />
                @else
                    <x-empty-state class="mt-4" icon="heroicon-o-arrow-trending-up" :title="__('jejak.not_available')" />
                @endif
                @if ($hideReadiness)
                    <p class="mt-3 flex items-start gap-2 text-sm text-slate-600">
                        <x-heroicon-o-lock-closed class="mt-0.5 h-5 w-5 shrink-0 text-slate-500" aria-hidden="true" />
                        {{ __('jejak.public_impact.privacy_note', ['count' => $minGroupSize]) }}
                    </p>
                @endif
                <x-data-table class="mt-4" :scroll="false" :caption="__('jejak.public_impact.funnel_title')">
                    <thead class="sr-only">
                        <tr>
                            <th scope="col">{{ __('jejak.public_impact.funnel_stage') }}</th>
                            <th scope="col">{{ __('jejak.charts.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($funnel as $stage)
                            <tr>
                                <th scope="row" class="py-2 pr-3 text-left font-normal text-slate-600">{{ $stage['label'] }}</th>
                                <td @class(['py-2 text-right tabular-nums', 'font-semibold text-navy-900' => $stage['value'] !== null, 'text-xs text-slate-500' => $stage['value'] === null])>{{ $stage['hidden'] ? __('jejak.public_impact.hidden') : Format::number($stage['value']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-data-table>
                <div class="mt-4 space-y-2 text-sm text-slate-600">
                    <p>{{ __('jejak.public_impact.funnel_mapping') }}</p>
                    <p>{{ __('jejak.public_impact.ready_note') }}</p>
                    @if (isset($filters['tahun']))
                        <p>{{ __('jejak.public_impact.ready_year_note') }}</p>
                    @endif
                    <p>{{ __('jejak.public_impact.beneficiaries_note') }}</p>
                </div>
            </div>

            <div class="min-w-0 rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-900/5">
                <h2 class="flex items-center gap-2 text-lg font-bold text-navy-900">
                    <x-heroicon-o-clipboard-document-check class="h-5 w-5 shrink-0 text-gold-700" aria-hidden="true" />
                    {{ __('jejak.public_impact.distribution_title') }}
                </h2>
                @if ($hideReadiness)
                    <x-empty-state class="mt-4" icon="heroicon-o-lock-closed" :title="__('jejak.public_impact.hidden')">
                        {{ __('jejak.public_impact.distribution_hidden', ['count' => $minGroupSize]) }}
                    </x-empty-state>
                @elseif ($levels === [])
                    <x-empty-state class="mt-4" icon="heroicon-o-clipboard-document-check" :title="__('jejak.not_available')">
                        {{ __('jejak.public_impact.distribution_empty') }}
                    </x-empty-state>
                @else
                    @foreach ($levels as $level)
                        @php
                            $caption = __('jejak.public_impact.distribution_chart', ['name' => $level['label']]);
                            $total = array_sum($level['counts']);
                        @endphp
                        <div class="mt-5">
                            <h3 class="text-sm font-semibold text-navy-900">{{ $level['label'] }}</h3>
                            <x-chart :config="$level['chart']" class="mt-2 h-44" role="img" :aria-label="$caption" />
                            <x-data-table class="mt-3" :scroll="false" :caption="$caption">
                                <thead class="sr-only">
                                    <tr>
                                        <th scope="col">{{ __('jejak.charts.category') }}</th>
                                        <th scope="col">{{ __('jejak.charts.amount') }}</th>
                                        <th scope="col">{{ __('jejak.charts.share') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($level['counts'] as $name => $count)
                                        <tr>
                                            <th scope="row" class="py-2 pr-3 text-left font-normal text-slate-600">{{ $name }}</th>
                                            <td class="py-2 text-right font-semibold tabular-nums text-navy-900">{{ Format::number($count) }}</td>
                                            <td class="w-14 py-2 text-right tabular-nums text-slate-500">{{ Format::share($count, $total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </x-data-table>
                        </div>
                    @endforeach
                @endif
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2" data-reveal>
            @foreach ($distributions as $distribution)
                <div class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-900/5">
                    <h2 class="flex items-center gap-2 text-lg font-bold text-navy-900">
                        <x-heroicon-o-chart-pie class="h-5 w-5 shrink-0 text-gold-700" aria-hidden="true" />
                        {{ $distribution['title'] }}
                    </h2>
                    @if ($distribution['chart'])
                        <x-donut-table class="mt-4" :chart="$distribution['chart']" :title="$distribution['title']" :unit="$distribution['unit']" />
                    @else
                        <x-empty-state class="mt-4" icon="heroicon-o-chart-pie" :title="__('jejak.not_available')" />
                    @endif
                </div>
            @endforeach
        </section>
    </div>
</x-layouts.public>
