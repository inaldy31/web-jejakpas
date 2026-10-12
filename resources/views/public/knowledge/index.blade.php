<x-layouts.public :title="__('jejak.nav.knowledge')">
    <x-page-hero :eyebrow="__('jejak.nav.knowledge')" width="5xl">
        <x-slot:title>
            {{ __('jejak.public_knowledge.hero_title_before') }}
            <span class="text-gold-400">{{ __('jejak.public_knowledge.hero_title_highlight') }}</span>
        </x-slot:title>
        <x-slot:lead>{{ __('jejak.public_knowledge.hero_description') }}</x-slot:lead>
    </x-page-hero>

    <div class="mx-auto mt-10 max-w-5xl space-y-10 px-4 pb-4" data-reveal>
        @forelse ($sections as $section)
            <section>
                <h2 class="flex items-center gap-2 text-xl font-bold text-navy-900">
                    <x-heroicon-o-document-text class="h-6 w-6 shrink-0 text-gold-700" aria-hidden="true" />
                    {{ $section['category']->getLabel() }}
                </h2>
                <ul class="mt-3 divide-y divide-slate-100 rounded-2xl bg-white shadow-soft ring-1 ring-slate-900/5">
                    @foreach ($section['items'] as $item)
                        <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="flex flex-wrap items-center gap-2 font-semibold text-navy-900">
                                    <span class="min-w-0 break-words">{{ $item->title }}</span>
                                    @if (($format = pathinfo($item->file_path, PATHINFO_EXTENSION)) !== '')
                                        <span class="rounded-full bg-navy-50 px-2.5 py-0.5 text-xs font-medium text-navy-900">{{ strtoupper($format) }}</span>
                                    @endif
                                </p>
                                @if ($item->description)
                                    <p class="mt-1 text-sm text-slate-600 [overflow-wrap:anywhere]">{{ $item->description }}</p>
                                @endif
                            </div>
                            <a href="{{ route('knowledge.download', $item) }}" class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-full bg-navy-900 px-5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-navy-800">
                                <x-heroicon-o-arrow-down-tray class="h-5 w-5" aria-hidden="true" />
                                {{ __('jejak.public_knowledge.download') }}<span class="sr-only">: {{ $item->title }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @empty
            <x-empty-state :title="__('jejak.public_knowledge.empty')" icon="heroicon-o-document-text">
                {{ __('jejak.public_knowledge.empty_hint') }}
            </x-empty-state>
        @endforelse
    </div>
</x-layouts.public>
