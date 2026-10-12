@props(['eyebrow' => null, 'large' => false, 'photos' => null, 'photosLabel' => null, 'width' => '7xl'])

@php
    $photos = collect($photos);
    $slideshow = $photos->count() > 1;
    $column = match ($width) {
        '3xl' => 'max-w-3xl',
        '5xl' => 'max-w-5xl',
        default => 'max-w-7xl',
    };
    $scope = $photos->isEmpty() ? [] : [
        'x-data' => 'heroSlideshow('.$photos->count().')',
        'x-on:pointerenter' => "hovered = \$event.pointerType === 'mouse'",
        'x-on:pointerleave' => 'hovered = false',
    ];
@endphp

<section {{ $attributes->class(['on-dark relative isolate overflow-hidden bg-navy-900 text-white'])->merge($scope) }}>
    @if ($photos->isNotEmpty())
        <div class="absolute inset-0 -z-10">
            <x-hero-photos.background :photos="$photos" />
        </div>
    @endif
    <svg class="absolute inset-0 -z-10 h-full w-full text-gold-400/10" aria-hidden="true">
        <defs>
            <pattern id="jejak-hero-pola" width="56" height="56" patternUnits="userSpaceOnUse">
                <path d="M0 56L56 0" stroke="currentColor" stroke-width="1" fill="none" />
                <circle cx="28" cy="28" r="1.5" fill="currentColor" />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#jejak-hero-pola)" />
    </svg>
    <div class="pointer-events-none absolute -right-24 -top-24 -z-10 h-80 w-80 rounded-full bg-gold-400/10 blur-3xl" aria-hidden="true"></div>

    <div @class([
        'mx-auto grid items-center gap-10 px-4 lg:grid-cols-12',
        $column,
        'py-16 md:py-24' => $large && ! $slideshow,
        'pt-16 pb-28 md:pt-24 md:pb-32' => $large && $slideshow,
        'py-12 md:py-16' => ! $large && ! $slideshow,
        'pt-12 pb-28 md:pt-16 md:pb-32' => ! $large && $slideshow,
    ])>
        <div @class(['min-w-0', 'lg:col-span-7' => isset($aside), 'lg:col-span-12' => ! isset($aside)])>
            @isset($before)
                {{ $before }}
            @endisset
            @if ($eyebrow)
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gold-400">{{ $eyebrow }}</p>
            @endif
            <h1 @class([
                'mt-3 font-extrabold tracking-tight text-balance break-words',
                'text-4xl md:text-6xl' => $large,
                'text-3xl md:text-5xl' => ! $large,
            ])>{{ $title }}</h1>
            @isset($lead)
                <div class="mt-4 max-w-2xl text-lg leading-relaxed text-slate-200">{{ $lead }}</div>
            @endisset
            @isset($actions)
                <div class="mt-8 flex flex-wrap items-center gap-3">{{ $actions }}</div>
            @endisset
        </div>
        @isset($aside)
            <div class="lg:col-span-5">{{ $aside }}</div>
        @endisset
    </div>
    @if ($slideshow)
        <x-hero-photos.controls :photos="$photos" :label="$photosLabel" />
    @endif
</section>
