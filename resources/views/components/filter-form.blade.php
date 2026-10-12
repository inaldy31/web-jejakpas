@props(['action', 'fields', 'filters' => [], 'layout' => 'stacked'])

@php
    $fieldsClass = match ($layout) {
        'grid' => 'mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4',
        'inline' => 'mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end',
        default => 'mt-4 space-y-4',
    };
    $actionsClass = match ($layout) {
        'grid' => 'flex flex-wrap gap-2 sm:col-span-2 lg:col-span-4',
        'inline' => 'flex flex-wrap gap-2 sm:col-span-2 lg:col-span-1',
        default => 'flex flex-wrap gap-2 pt-1',
    };
@endphp

<form id="filter" method="GET" action="{{ $action }}#hasil" class="rounded-2xl bg-white p-5 shadow-soft ring-1 ring-slate-900/5">
    <h2 class="flex items-center gap-2 font-semibold text-navy-900">
        <x-heroicon-o-funnel class="h-5 w-5 text-gold-700" aria-hidden="true" />
        {{ __('jejak.public.filter.heading') }}
    </h2>
    @if (isset($errors) && $errors->any())
        <div role="alert" class="mt-3 flex items-start gap-2 rounded-lg bg-rose-50 p-3 text-sm text-rose-800 ring-1 ring-rose-200">
            <x-heroicon-o-exclamation-triangle class="mt-0.5 h-5 w-5 shrink-0" aria-hidden="true" />
            <div>
                <p class="font-semibold">{{ __('jejak.public.filter.errors_title') }}</p>
                <ul class="mt-1 list-disc space-y-0.5 pl-5">
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    <div class="{{ $fieldsClass }}">
        @foreach ($fields as $field)
            <label class="block text-sm">
                <span class="font-medium text-slate-700">{{ $field['label'] }}</span>
                <select name="{{ $field['name'] }}" class="mt-1.5 h-11 w-full rounded-lg border border-slate-500 bg-white px-3 text-base text-slate-800 transition-colors duration-200 hover:border-slate-600 focus:border-navy-900 sm:text-sm">
                    <option value="">{{ $field['all'] ?? __('jejak.public.filter.all') }}</option>
                    @foreach ($field['options'] as $value => $text)
                        <option value="{{ $value }}" @selected((string) ($filters[$field['name']] ?? '') === (string) $value)>{{ $text }}</option>
                    @endforeach
                </select>
            </label>
        @endforeach
        <div class="{{ $actionsClass }}">
            <button type="submit" class="inline-flex h-11 items-center rounded-full bg-navy-900 px-5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-navy-800">{{ __('jejak.public.filter.apply') }}</button>
            <a href="{{ $action }}#hasil" class="inline-flex h-11 items-center rounded-full px-4 text-sm font-medium text-slate-600 transition-colors duration-200 hover:bg-slate-100 hover:text-navy-900">{{ __('jejak.public.filter.reset') }}</a>
        </div>
    </div>
    @if ($slot->hasActualContent())
        <p class="mt-4 flex items-start gap-2 text-sm text-slate-600">
            <x-heroicon-o-information-circle class="mt-0.5 h-5 w-5 shrink-0 text-slate-500" aria-hidden="true" />
            <span>{{ $slot }}</span>
        </p>
    @endif
</form>
