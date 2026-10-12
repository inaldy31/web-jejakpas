@props(['title', 'icon' => 'heroicon-o-magnifying-glass'])

<div role="status" {{ $attributes->class(['flex flex-col items-center rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center']) }}>
    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-navy-50 text-navy-900">
        <x-dynamic-component :component="$icon" class="h-6 w-6" aria-hidden="true" />
    </span>
    <p class="mt-4 font-semibold text-navy-900">{{ $title }}</p>
    @if ($slot->hasActualContent())
        <div class="mt-1 max-w-md text-sm text-slate-600">{{ $slot }}</div>
    @endif
    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>
