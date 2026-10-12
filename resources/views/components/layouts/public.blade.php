@props(['title' => null])

@php
    $navigation = [
        ['route' => 'home', 'active' => 'home', 'label' => __('jejak.nav.home')],
        ['route' => 'programs.index', 'active' => 'programs.*', 'label' => __('jejak.nav.programs')],
        ['route' => 'partners.index', 'active' => 'partners.*', 'label' => __('jejak.nav.partners')],
        ['route' => 'impact', 'active' => 'impact', 'label' => __('jejak.nav.impact')],
        ['route' => 'knowledge.index', 'active' => 'knowledge.*', 'label' => __('jejak.nav.knowledge')],
    ];
    $information = [
        ['route' => 'terms', 'label' => __('jejak.footer.terms')],
        ['route' => 'privacy', 'label' => __('jejak.footer.privacy')],
        ['route' => 'contact', 'label' => __('jejak.footer.contact')],
    ];
    $isActive = fn (array $item): bool => request()->routeIs($item['active']);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' | ' : '' }}{{ __('jejak.app_name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="16x16 32x32 48x48">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 antialiased">
    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:font-semibold focus:text-navy-900 focus:shadow-lift">{{ __('jejak.nav.skip_to_content') }}</a>

    <header class="sticky top-0 z-30 bg-white/90 backdrop-blur transition-shadow duration-200"
            x-data="{ open: false, scrolled: false }"
            x-init="scrolled = window.scrollY > 8"
            @scroll.window.passive="scrolled = window.scrollY > 8"
            @keydown.escape.window="if (open) { open = false; $refs.menuButton.focus() }"
            @focusout="if ($event.relatedTarget && ! $el.contains($event.relatedTarget)) open = false"
            :class="scrolled ? 'shadow-soft' : ''">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3">
            <a href="{{ route('home') }}" class="flex min-h-11 min-w-0 items-center gap-2 rounded-lg">
                <span class="flex shrink-0 items-center gap-1.5">
                    <img src="{{ asset('images/lambang-imipas.png') }}" alt="" width="128" height="128" class="h-9 w-9 sm:h-10 sm:w-10">
                    <img src="{{ asset('images/lambang-pemasyarakatan.png') }}" alt="" width="135" height="128" class="h-9 w-auto sm:h-10">
                </span>
                <span class="min-w-0 leading-tight">
                    <span class="block text-xl font-extrabold whitespace-nowrap text-navy-900" data-brand-wordmark>JEJAK <span class="text-gold-500">PAS</span></span>
                    <span class="hidden max-w-56 text-xs text-slate-600 sm:block">{{ __('jejak.tagline') }}</span>
                </span>
                <span class="sr-only">, {{ __('jejak.nav.home') }}</span>
            </a>

            <div class="flex shrink-0 items-center gap-3">
                <nav aria-label="{{ __('jejak.nav.main') }}" class="hidden items-center gap-5 text-sm font-medium xl:flex">
                    @foreach ($navigation as $item)
                        <a href="{{ route($item['route']) }}" @if ($isActive($item)) aria-current="page" @endif
                           @class(['inline-flex min-h-11 items-center whitespace-nowrap border-b-2 transition-colors duration-200', 'border-gold-700 font-semibold text-navy-900' => $isActive($item), 'border-transparent text-slate-600 hover:text-navy-900' => ! $isActive($item)])>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('filament.admin.auth.login') }}" class="inline-flex h-11 items-center whitespace-nowrap rounded-full bg-navy-900 px-5 font-semibold text-white transition-colors duration-200 hover:bg-navy-800">{{ __('jejak.nav.login') }}</a>
                </nav>

                <button type="button" x-ref="menuButton" class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-navy-900 transition-colors duration-200 hover:bg-slate-100 xl:hidden"
                        @click="open = !open" aria-controls="menu-seluler" :aria-expanded="open.toString()" aria-label="{{ __('jejak.nav.menu') }}">
                    <x-heroicon-o-bars-3 class="h-6 w-6" x-show="!open" aria-hidden="true" />
                    <x-heroicon-o-x-mark class="h-6 w-6" x-show="open" x-cloak aria-hidden="true" />
                </button>
            </div>
        </div>

        <nav id="menu-seluler" aria-label="{{ __('jejak.nav.main') }}" class="max-h-[calc(100dvh-var(--header-height))] overflow-y-auto border-t border-slate-100 px-4 pb-4 pt-2 xl:hidden" x-show="open" x-cloak x-transition.opacity>
            @foreach ($navigation as $item)
                <a href="{{ route($item['route']) }}" @if ($isActive($item)) aria-current="page" @endif
                   @class(['flex min-h-11 items-center rounded-r-lg border-l-4 px-3 text-sm', 'border-gold-700 bg-navy-50 font-semibold text-navy-900' => $isActive($item), 'border-transparent font-medium text-slate-700 hover:bg-slate-50' => ! $isActive($item)])>
                    {{ $item['label'] }}
                </a>
            @endforeach
            <a href="{{ route('filament.admin.auth.login') }}" class="mt-3 inline-flex h-11 items-center rounded-full bg-navy-900 px-5 text-sm font-semibold text-white">{{ __('jejak.nav.login') }}</a>
        </nav>
    </header>

    <main id="konten" tabindex="-1" class="flex-1 focus:outline-none">
        {{ $slot }}
    </main>

    <footer class="on-dark mt-20 border-t-4 border-gold-400 bg-navy-900 text-slate-300">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 md:grid-cols-3">
            <div>
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/lambang-imipas.png') }}" alt="{{ __('jejak.logos.ministry') }}" width="128" height="128" class="h-10 w-10">
                    <img src="{{ asset('images/lambang-pemasyarakatan.png') }}" alt="{{ __('jejak.logos.corrections') }}" width="135" height="128" class="h-10 w-auto">
                    <span class="text-lg font-extrabold text-white">JEJAK <span class="text-gold-400">PAS</span></span>
                </div>
                <p class="mt-4 max-w-xs text-sm leading-relaxed">{{ __('jejak.tagline') }}</p>
            </div>
            <nav aria-label="{{ __('jejak.footer.navigation') }}">
                <p class="text-sm font-semibold text-white">{{ __('jejak.footer.navigation') }}</p>
                <ul class="mt-2 text-sm">
                    @foreach ($navigation as $item)
                        <li><a href="{{ route($item['route']) }}" class="inline-flex min-h-11 items-center transition-colors duration-200 hover:text-gold-400">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>
            <nav aria-label="{{ __('jejak.footer.information') }}">
                <p class="text-sm font-semibold text-white">{{ __('jejak.footer.information') }}</p>
                <ul class="mt-2 text-sm">
                    @foreach ($information as $item)
                        <li><a href="{{ route($item['route']) }}" class="inline-flex min-h-11 items-center transition-colors duration-200 hover:text-gold-400">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>
        </div>
        <div class="border-t border-white/10">
            <p class="mx-auto max-w-7xl px-4 py-5 text-xs text-slate-400">© {{ now()->year }} {{ __('jejak.ministry') }}</p>
        </div>
    </footer>
</body>
</html>
