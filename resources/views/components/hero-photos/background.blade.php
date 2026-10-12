@props(['photos'])

@foreach ($photos as $index => $photo)
    <img @if ($index === 0) src="{{ asset($photo['file']) }}" @else data-src="{{ asset($photo['file']) }}" x-bind:src="loaded.includes({{ $index }}) ? $el.dataset.src : null" @endif alt="{{ $photos->count() === 1 ? $photo['caption'] : '' }}" @class(['absolute inset-0 h-full w-full object-cover motion-safe:transition-opacity motion-safe:duration-1000 motion-safe:ease-out', 'opacity-0' => $index > 0]) x-bind:class="{ 'opacity-0': shown !== {{ $index }} }" x-on:load="photoReady({{ $index }})" x-on:error="photoReady({{ $index }})" @if ($index === 0) fetchpriority="high" @endif>
@endforeach
<div class="absolute inset-0 bg-navy-900/94 lg:bg-transparent lg:bg-gradient-to-r lg:from-navy-900 lg:via-navy-900/95 lg:to-navy-900/85"></div>
<div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-navy-900/80 to-transparent"></div>
