@php
    use App\Support\HeroPhotos;
    use App\Support\StatIcons;

    $statCards = ['pilot_upts', 'programs', 'participants', 'certified', 'peer_educators', 'partners'];
    $journeyIcons = [
        'heroicon-o-wrench-screwdriver',
        'heroicon-o-check-badge',
        'heroicon-o-clipboard-document-check',
        'heroicon-o-light-bulb',
        'heroicon-o-academic-cap',
        'heroicon-o-arrow-trending-up',
    ];
    $heroPhotos = HeroPhotos::available('jejak.hero_photos');
@endphp

<x-layouts.public>
    <x-page-hero :eyebrow="__('jejak.home.eyebrow')" :large="true" :photos="$heroPhotos" :photos-label="__('jejak.hero_photos.home_label')">
        <x-slot:title>JEJAK <span class="text-gold-400">PAS</span></x-slot:title>
        <x-slot:lead>
            <p class="font-semibold text-white md:text-xl">{{ __('jejak.tagline') }}</p>
            <p class="mt-3">{{ __('jejak.home.description') }}</p>
        </x-slot:lead>
        <x-slot:actions>
            <a href="{{ route('partners.index') }}" class="inline-flex h-12 items-center gap-2 rounded-full bg-gold-400 px-6 font-semibold text-navy-900 shadow-soft transition-colors duration-200 hover:bg-gold-500">
                {{ __('jejak.home.cta_partners') }}
                <x-heroicon-o-arrow-right class="h-5 w-5" aria-hidden="true" />
            </a>
            <a href="{{ route('contact') }}" class="inline-flex h-12 items-center rounded-full border border-white/60 px-6 font-semibold text-white transition-colors duration-200 hover:bg-white/10">
                {{ __('jejak.home.cta_partner') }}
            </a>
        </x-slot:actions>
        <x-slot:aside>
            <div class="lg:pl-6">
                <blockquote class="border-l-4 border-gold-400 pl-4 text-lg italic text-slate-100">“{{ __('jejak.home.quote') }}”</blockquote>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <section class="relative z-10 mx-auto -mt-12 max-w-7xl px-4">
        <div class="grid grid-cols-2 gap-2 rounded-2xl bg-white p-3 shadow-lift ring-1 ring-slate-900/5 md:grid-cols-3 lg:grid-cols-6">
            @foreach ($statCards as $key)
                <x-stat-card :label="__('jejak.stats.'.$key)" :value="$summary[$key]" :icon="StatIcons::for($key)" />
            @endforeach
        </div>
        <div class="mt-3 flex justify-end">
            <a href="{{ route('impact') }}" class="inline-flex min-h-11 items-center gap-1.5 rounded-lg px-2 text-sm font-semibold text-navy-900 transition-colors duration-200 hover:text-navy-700">
                {{ __('jejak.home.cta_impact') }}
                <x-heroicon-o-arrow-right class="h-4 w-4" aria-hidden="true" />
            </a>
        </div>
    </section>

    <section class="mx-auto mt-16 max-w-7xl px-4">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gold-700">{{ __('jejak.home.journey_eyebrow') }}</p>
            <h2 class="mt-2 text-2xl font-bold text-navy-900 md:text-3xl">{{ __('jejak.home.journey_title') }}</h2>
            <p class="mt-2 text-slate-600">{{ __('jejak.home.journey_subtitle') }}</p>
        </div>
        <div class="relative mt-10">
            <div class="pointer-events-none absolute inset-x-10 top-11 hidden h-px bg-gold-400/60 lg:block" aria-hidden="true"></div>
            <ol class="relative grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
                @foreach (__('jejak.journey') as $index => $step)
                    <li class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-900/5" data-reveal style="--reveal-delay: {{ $index * 80 }}ms">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-navy-900 text-gold-400">
                                <x-dynamic-component :component="$journeyIcons[$index] ?? 'heroicon-o-sparkles'" class="h-6 w-6" aria-hidden="true" />
                            </span>
                            <span class="text-xs font-bold uppercase tracking-wider text-gold-700">{{ __('jejak.home.journey_step', ['number' => $index + 1]) }}</span>
                        </div>
                        <p class="mt-4 font-semibold text-navy-900">{{ $step['title'] }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="mx-auto mt-16 grid max-w-7xl gap-6 px-4 lg:grid-cols-5">
        <div class="rounded-2xl bg-white p-8 shadow-soft ring-1 ring-slate-900/5 lg:col-span-3" data-reveal>
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gold-50 text-gold-700">
                <x-heroicon-o-sparkles class="h-6 w-6" aria-hidden="true" />
            </span>
            <h2 class="mt-4 text-xl font-bold text-navy-900">{{ __('jejak.home.about_title') }}</h2>
            <p class="mt-3 leading-relaxed text-slate-700">{{ __('jejak.home.about_body') }}</p>
        </div>
        <div class="rounded-2xl bg-white p-8 shadow-soft ring-1 ring-slate-900/5 lg:col-span-2" data-reveal style="--reveal-delay: 100ms">
            <h2 class="text-xl font-bold text-navy-900">{{ __('jejak.home.pilot_title') }}</h2>
            <p class="mt-1 text-sm text-slate-600">
                @if ($pilotUpts->isEmpty())
                    {{ __('jejak.public.pilot_empty') }}
                @else
                    {{ __('jejak.home.pilot_intro', ['count' => $pilotUpts->count()]) }}
                @endif
            </p>
            @if ($pilotUpts->isNotEmpty())
                <ul class="mt-5 space-y-3">
                    @foreach ($pilotUpts as $upt)
                        <li class="flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-navy-900 text-gold-400">
                                <x-heroicon-o-map-pin class="h-5 w-5" aria-hidden="true" />
                            </span>
                            <span>
                                <span class="block font-semibold text-navy-900">{{ $upt->name }}</span>
                                <span class="text-sm text-slate-600">{{ $upt->province }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    <section class="mx-auto mt-16 max-w-7xl px-4">
        <div class="on-dark relative isolate overflow-hidden rounded-3xl bg-navy-900 px-6 py-12 text-center text-white md:px-12" data-reveal>
            <div class="pointer-events-none absolute -left-16 -top-16 -z-10 h-56 w-56 rounded-full bg-gold-400/15 blur-3xl" aria-hidden="true"></div>
            <h2 class="text-2xl font-bold md:text-3xl">{{ __('jejak.home.cta_band_title') }}</h2>
            <p class="mx-auto mt-3 max-w-2xl text-slate-200">{{ __('jejak.home.cta_band_text') }}</p>
            <a href="{{ route('contact') }}" class="mt-8 inline-flex h-12 items-center gap-2 rounded-full bg-gold-400 px-6 font-semibold text-navy-900 transition-colors duration-200 hover:bg-gold-500">
                {{ __('jejak.home.cta_partner') }}
                <x-heroicon-o-arrow-right class="h-5 w-5" aria-hidden="true" />
            </a>
        </div>
    </section>
</x-layouts.public>
