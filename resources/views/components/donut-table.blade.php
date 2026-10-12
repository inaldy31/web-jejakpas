@props(['chart', 'title', 'unit', 'stacked' => false])

@php
    use App\Support\Format;

    $labels = $chart['data']['labels'];
    $counts = $chart['data']['datasets'][0]['data'];
    $colors = $chart['data']['datasets'][0]['backgroundColor'];
    $total = array_sum($counts);
@endphp

<div {{ $attributes->class(['flex flex-col items-center gap-5', 'sm:flex-row' => ! $stacked]) }}>
    <div class="relative h-48 w-48 shrink-0">
        <x-chart :config="$chart" class="h-full" role="img" :aria-label="__('jejak.charts.donut_label', ['title' => $title, 'count' => Format::number($total), 'unit' => $unit])" />
        <div class="pointer-events-none absolute inset-0 flex items-center justify-center text-center" aria-hidden="true" data-donut-total>
            <span>
                <span class="block text-3xl font-extrabold tabular-nums leading-none text-navy-900">{{ Format::number($total) }}</span>
                <span class="mt-1 block text-xs font-medium text-slate-600">{{ $unit }}</span>
            </span>
        </div>
    </div>
    <x-data-table class="min-w-0 flex-1" :scroll="false" :caption="$title">
        <thead class="sr-only">
            <tr>
                <th scope="col">{{ __('jejak.charts.category') }}</th>
                <th scope="col">{{ __('jejak.charts.amount') }}</th>
                <th scope="col">{{ __('jejak.charts.share') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($labels as $index => $name)
                <tr>
                    <th scope="row" class="py-2 pr-3 text-left font-normal text-slate-600">
                        <span class="mr-2 inline-block h-3 w-3 rounded-sm align-middle" style="background-color: {{ $colors[$index] }}" aria-hidden="true"></span>{{ $name }}
                    </th>
                    <td class="py-2 text-right font-semibold tabular-nums text-navy-900">{{ Format::number($counts[$index]) }}</td>
                    <td class="w-14 py-2 text-right tabular-nums text-slate-600">{{ Format::share($counts[$index], $total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </x-data-table>
</div>
