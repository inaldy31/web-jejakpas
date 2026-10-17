@php
    use App\Support\HeroPhotos;

    $filterFields = [
        ['name' => 'kelompok', 'label' => __('jejak.partners.group'), 'options' => $groups],
        ['name' => 'status_pks', 'label' => __('jejak.partners.pks_status'), 'options' => $pksStatuses],
        ['name' => 'bidang', 'label' => __('jejak.partners.competency_fields'), 'options' => $fields],
        ['name' => 'upt', 'label' => __('jejak.upts.model_label'), 'options' => $upts],
        ['name' => 'wilayah', 'label' => __('jejak.public_partners.filter_wilayah'), 'options' => $provinces],
    ];

    $heroPhotos = HeroPhotos::available('jejak.partner_hero_photos');
@endphp

<x-layouts.public :title="__('jejak.nav.partners')">
    <x-page-hero :eyebrow="__('jejak.nav.partners')" :photos="$heroPhotos" :photos-label="__('jejak.hero_photos.partners_label')">
        <x-slot:title>
            {{ __('jejak.public_partners.hero_title_before') }}
            <span class="text-gold-400">{{ __('jejak.public_partners.hero_title_highlight') }}</span>
            {{ __('jejak.public_partners.hero_title_after') }}
        </x-slot:title>
        <x-slot:lead>{{ __('jejak.public_partners.hero_description') }}</x-slot:lead>
    </x-page-hero>

    <div class="mx-auto mt-10 grid max-w-7xl gap-8 px-4 lg:grid-cols-4">
        <div class="space-y-6 lg:col-span-1">
            <x-filter-form :action="route('partners.index')" :fields="$filterFields" :filters="$filters" />

            @if ($groupChart)
                <section aria-labelledby="kategori-mitra" class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-900/5">
                    <h2 id="kategori-mitra" class="flex items-center gap-2 font-semibold text-navy-900">
                        <x-heroicon-o-chart-pie class="h-5 w-5 text-gold-700" aria-hidden="true" />
                        {{ __('jejak.public_partners.chart_group_title') }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-600">{{ __('jejak.public_partners.chart_group_note') }}</p>
                    <x-donut-table class="mt-4" :chart="$groupChart" :title="__('jejak.public_partners.chart_group_title')" :unit="__('jejak.public_partners.unit')" stacked />
                </section>
            @endif
        </div>

        <section class="lg:col-span-3">
            <h2 id="hasil" class="flex items-center gap-2 text-sm font-medium text-slate-600">
                <x-heroicon-o-building-office-2 class="h-5 w-5 text-slate-500" aria-hidden="true" />
                {{ __('jejak.public_partners.count', ['count' => $partners->total()]) }}
            </h2>
            <div class="mt-4 grid gap-5 sm:grid-cols-2 xl:grid-cols-3" data-reveal>
                @forelse ($partners as $partner)
                    <article class="group relative flex min-w-0 flex-col rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-900/5 transition duration-200 hover:shadow-lift motion-safe:hover:-translate-y-0.5">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-navy-50 text-navy-900">
                                <x-heroicon-o-building-office-2 class="h-5 w-5" aria-hidden="true" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gold-700">{{ $partner->group?->getLabel() }}</p>
                                <h3 class="mt-0.5 break-words font-bold text-navy-900 transition-colors duration-200 group-hover:text-navy-700">
                                    <a href="{{ route('partners.show', $partner) }}" class="after:absolute after:inset-0">{{ $partner->name }}</a>
                                </h3>
                            </div>
                        </div>
                        @if ($partner->competencyLabels() !== [])
                            <ul class="mt-4 flex flex-wrap gap-1.5">
                                @foreach ($partner->competencyLabels() as $field)
                                    <li class="rounded-full bg-navy-50 px-2.5 py-0.5 text-xs font-medium text-navy-900">{{ $field }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if ($partner->upts->isNotEmpty())
                            <p class="mt-3 flex items-start gap-1.5 text-xs text-slate-600">
                                <x-heroicon-o-map-pin class="mt-px h-4 w-4 shrink-0 text-slate-500" aria-hidden="true" />
                                <span>{{ $partner->upts->pluck('name')->implode(', ') }}</span>
                            </p>
                        @endif
                        <div class="mt-auto flex items-center justify-between gap-2 pt-4">
                            @if ($partner->pks_status)
                                <x-pks-badge :status="$partner->pks_status" />
                            @else
                                <span></span>
                            @endif
                            <x-heroicon-o-arrow-right class="h-5 w-5 text-navy-900 transition-transform duration-200 motion-safe:group-hover:translate-x-1" aria-hidden="true" />
                        </div>
                    </article>
                @empty
                    <x-empty-state class="sm:col-span-2 xl:col-span-3" :title="__('jejak.public_partners.empty')">
                        {{ __('jejak.public_partners.empty_hint') }}
                        <x-slot:action>
                            <a href="{{ route('partners.index') }}" class="inline-flex h-11 items-center rounded-full bg-navy-900 px-5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-navy-800">{{ __('jejak.public.filter.reset') }}</a>
                        </x-slot:action>
                    </x-empty-state>
                @endforelse
            </div>
            <div class="mt-8">{{ $partners->links() }}</div>
        </section>
    </div>
</x-layouts.public>
