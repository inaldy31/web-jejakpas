@props(['caption', 'scroll' => true])

@php
    $id = 'tabel-'.substr(md5($caption.$slot), 0, 8);
@endphp

@if ($scroll)
    <div role="region" tabindex="0" aria-labelledby="{{ $id }}" {{ $attributes->class(['scroll-cue overflow-x-auto']) }}>
        <table class="min-w-full text-sm">
            <caption id="{{ $id }}" class="sr-only">{{ $caption }}</caption>
            {{ $slot }}
        </table>
    </div>
@else
    <table {{ $attributes->class(['w-full text-sm']) }}>
        <caption class="sr-only">{{ $caption }}</caption>
        {{ $slot }}
    </table>
@endif
