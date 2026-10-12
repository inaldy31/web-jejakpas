@props(['label', 'value', 'decimals' => 0, 'icon' => null])

<div {{ $attributes->merge(['class' => 'rounded-xl bg-white p-4 text-center']) }}>
    @if ($icon)
        <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-gold-50 text-gold-700">
            <x-dynamic-component :component="$icon" class="h-5 w-5" aria-hidden="true" />
        </span>
    @endif
    <div @class(['font-extrabold tabular-nums', 'text-2xl text-navy-900' => $value !== null, 'text-sm text-slate-500' => $value === null])>
        {{ \App\Support\Format::number($value, $decimals) }}
    </div>
    <div class="mt-1 text-xs font-medium text-slate-600">{{ $label }}</div>
</div>
