@php
    use App\Support\Format;
    use App\Support\StatIcons;
@endphp

<x-layouts.public :title="$partner->name">
    <x-page-hero :eyebrow="$partner->group?->getLabel()">
        <x-slot:before>
            <nav aria-label="{{ __('jejak.public_partners.breadcrumb') }}" class="mb-6 text-sm">
                <ol class="flex flex-wrap items-center gap-x-2 text-slate-300">
                    <li>
                        <a href="{{ route('partners.index') }}" class="inline-flex min-h-11 items-center gap-1.5 font-medium text-white transition-colors duration-200 hover:text-gold-400">
                            <x-heroicon-o-arrow-left class="h-4 w-4" aria-hidden="true" />
                            {{ __('jejak.nav.partners') }}
                        </a>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li aria-current="page" class="min-w-0 truncate">{{ $partner->name }}</li>
                </ol>
            </nav>
        </x-slot:before>
        <x-slot:title>{{ $partner->name }}</x-slot:title>
        @if ($partner->pks_status || $partner->sustainability_status)
            <x-slot:actions>
                @if ($partner->pks_status)
                    <x-pks-badge :status="$partner->pks_status" />
                @endif
                @if ($partner->sustainability_status)
                    <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white ring-1 ring-inset ring-white/20">
                        {{ __('jejak.public_partners.sustainability_badge', ['status' => $partner->sustainability_status->getLabel()]) }}
                    </span>
                @endif
            </x-slot:actions>
        @endif
    </x-page-hero>

    <div class="mx-auto mt-10 grid max-w-7xl gap-6 px-4 lg:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-900/5 md:p-8 lg:col-span-2">
            <dl class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                @foreach ([
                    ['heroicon-o-map-pin', __('jejak.public_partners.filter_wilayah'), filled($partner->province) ? $partner->province : '—'],
                    ['heroicon-o-building-office-2', __('jejak.partners.upts'), $partner->upts->pluck('name')->implode(', ') ?: '—'],
                    ['heroicon-o-document-text', __('jejak.partners.pks_status'), $partner->pks_status?->getLabel() ?? '—'],
                    ['heroicon-o-calendar-days', __('jejak.partners.pks_valid_until'), $partner->pks_valid_until?->translatedFormat('d F Y') ?? '—'],
                    ['heroicon-o-arrow-path', __('jejak.public_partners.sustainability_status'), $partner->sustainability_status?->getLabel() ?? '—'],
                    ['heroicon-o-user-circle', __('jejak.partners.liaison_position'), $partner->liaison_position ?? '—'],
                ] as [$icon, $term, $detail])
                    <div>
                        <dt class="flex items-center gap-2 text-sm text-slate-500">
                            <x-dynamic-component :component="$icon" class="h-5 w-5 text-navy-900" aria-hidden="true" />
                            {{ $term }}
                        </dt>
                        <dd class="mt-1 pl-7 font-medium text-slate-800">{{ $detail }}</dd>
                    </div>
                @endforeach
                <div class="sm:col-span-2">
                    <dt class="flex items-center gap-2 text-sm text-slate-500">
                        <x-heroicon-o-sparkles class="h-5 w-5 text-navy-900" aria-hidden="true" />
                        {{ __('jejak.partners.competency_fields') }}
                    </dt>
                    <dd class="mt-2 flex flex-wrap gap-1.5 pl-7">
                        @forelse ($partner->competencyLabels() as $label)
                            <span class="rounded-full bg-navy-50 px-2.5 py-0.5 text-xs font-medium text-navy-900">{{ $label }}</span>
                        @empty
                            <span class="font-medium text-slate-800">—</span>
                        @endforelse
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="flex items-center gap-2 text-sm text-slate-500">
                        <x-heroicon-o-hand-raised class="h-5 w-5 text-navy-900" aria-hidden="true" />
                        {{ __('jejak.partners.support_types') }}
                    </dt>
                    <dd class="mt-2 flex flex-wrap gap-1.5 pl-7">
                        @forelse ($partner->supportTypeLabels() as $label)
                            <span class="rounded-full bg-gold-50 px-2.5 py-0.5 text-xs font-medium text-gold-700">{{ $label }}</span>
                        @empty
                            <span class="font-medium text-slate-800">—</span>
                        @endforelse
                    </dd>
                </div>
            </dl>

            @if ($partner->scope)
                <div class="mt-8 border-t border-slate-100 pt-6">
                    <h2 class="flex items-center gap-2 font-semibold text-navy-900">
                        <x-heroicon-o-clipboard-document-check class="h-5 w-5 text-gold-700" aria-hidden="true" />
                        {{ __('jejak.public_partners.scope_title') }}
                    </h2>
                    <p class="mt-2 leading-relaxed text-slate-700">{{ $partner->scope }}</p>
                </div>
            @endif
            @if ($partner->best_practice_summary)
                <div class="mt-8 border-t border-slate-100 pt-6">
                    <h2 class="flex items-center gap-2 font-semibold text-navy-900">
                        <x-heroicon-o-light-bulb class="h-5 w-5 text-gold-700" aria-hidden="true" />
                        {{ __('jejak.public_partners.best_practice_title') }}
                    </h2>
                    <p class="mt-2 leading-relaxed text-slate-700">{{ $partner->best_practice_summary }}</p>
                </div>
            @endif
        </div>

        <div class="self-start rounded-2xl bg-white p-3 shadow-soft ring-1 ring-slate-900/5">
            <div class="grid grid-cols-2 gap-2">
                <x-stat-card :label="__('jejak.public_partners.stat_trainings')" :value="$partner->total_trainings" :icon="StatIcons::for('programs')" />
                <x-stat-card :label="__('jejak.public_partners.stat_participants')" :value="$partner->total_participants" :icon="StatIcons::for('participants')" />
                <x-stat-card :label="__('jejak.public_partners.stat_certified')" :value="$partner->total_certified" :icon="StatIcons::for('certified')" />
                <x-stat-card :label="__('jejak.stats.beneficiaries')" :value="$partner->total_beneficiaries" :icon="StatIcons::for('beneficiaries')" />
            </div>
        </div>
    </div>

    @if ($programs->isNotEmpty())
        <section class="mx-auto mt-10 max-w-7xl px-4">
            <h2 class="flex items-center gap-2 text-xl font-bold text-navy-900">
                <x-heroicon-o-book-open class="h-6 w-6 text-gold-700" aria-hidden="true" />
                {{ __('jejak.public_partners.programs_title') }}
            </h2>
            <x-data-table class="mt-4 rounded-2xl bg-white shadow-soft ring-1 ring-slate-900/5" :caption="__('jejak.public_partners.programs_title')">
                <thead class="bg-navy-900 text-left text-white">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-semibold">{{ __('jejak.programs.columns.year') }}</th>
                        <th scope="col" class="px-4 py-3 font-semibold">{{ __('jejak.public_partners.stat_trainings') }}</th>
                        <th scope="col" class="px-4 py-3 font-semibold">{{ __('jejak.upts.model_label') }}</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold">{{ __('jejak.public_partners.stat_participants') }}</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold">{{ __('jejak.public_partners.stat_certified') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($programs as $program)
                        <tr class="transition-colors duration-150 hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 tabular-nums">{{ $program->year }}</td>
                            <th scope="row" class="px-4 py-3 text-left font-medium text-navy-900">{{ $program->name }}</th>
                            <td class="whitespace-nowrap px-4 py-3">{{ $program->upt->short_name }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ Format::number($program->total_participants) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ Format::number($program->total_certified) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-data-table>
        </section>
    @endif
</x-layouts.public>
