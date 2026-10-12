@props(['status'])

@php
    $tone = match ($status->getColor()) {
        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'warning' => 'bg-amber-50 text-amber-800 ring-amber-600/20',
        'danger' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
        default => 'bg-slate-100 text-slate-700 ring-slate-500/20',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset', $tone]) }}>
    {{ __('jejak.public_partners.pks_badge', ['status' => $status->getLabel()]) }}
</span>
