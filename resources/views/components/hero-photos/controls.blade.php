@props(['photos', 'label'])

<div x-cloak role="group" aria-roledescription="{{ __('jejak.hero_photos.roledescription') }}" aria-label="{{ $label }}" class="absolute inset-x-0 bottom-14 flex justify-center md:bottom-16" x-on:focusin="focused = $event.target.matches(':focus-visible')" x-on:focusout="focused = false">
    <button type="button" class="mr-2 inline-flex h-11 w-11 cursor-pointer items-center justify-center rounded-full text-white transition-colors duration-200 hover:bg-white/10" x-on:click="toggle()" data-label-pause="{{ __('jejak.hero_photos.pause') }}" data-label-play="{{ __('jejak.hero_photos.play') }}" x-bind:aria-label="playing ? $el.dataset.labelPause : $el.dataset.labelPlay" aria-label="{{ __('jejak.hero_photos.pause') }}">
        <x-heroicon-o-pause class="h-5 w-5" aria-hidden="true" x-show="playing" />
        <x-heroicon-o-play class="h-5 w-5" aria-hidden="true" x-show="!playing" />
    </button>
    @foreach ($photos as $index => $photo)
        <button type="button" aria-label="{{ __('jejak.hero_photos.show', ['number' => $index + 1, 'caption' => $photo['caption']]) }}" class="group inline-flex h-11 w-11 cursor-pointer items-center justify-center rounded-full" x-on:click="pick({{ $index }})" x-bind:aria-current="current === {{ $index }}">
            <span class="h-2.5 rounded-full transition-all duration-200 ease-out forced-colors:border forced-colors:border-[CanvasText]" x-bind:class="current === {{ $index }} ? 'w-6 bg-gold-400 forced-colors:bg-[CanvasText]' : 'w-2.5 bg-white/50 group-hover:bg-white/80'"></span>
        </button>
    @endforeach
</div>
