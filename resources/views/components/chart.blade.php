@props(['config'])

@php
    $hasHeight = collect(preg_split('/\s+/', trim((string) $attributes->get('class', ''))))
        ->filter()
        ->contains(fn (string $token): bool => str_starts_with($token, 'h-'));
@endphp

<div {{ $attributes->class(['relative', 'h-64' => ! $hasHeight]) }}>
    <canvas data-chart="{{ json_encode($config) }}" class="w-full"></canvas>
</div>
