@if ($paginator->hasPages())
    <nav aria-label="{{ __('pagination.navigation') }}" class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
        <p class="text-sm text-slate-600">
            {{ __('pagination.showing', ['first' => $paginator->firstItem(), 'last' => $paginator->lastItem(), 'total' => $paginator->total()]) }}
        </p>

        <ul class="flex flex-wrap items-center justify-center gap-1">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="inline-flex h-11 min-w-11 items-center justify-center gap-1 rounded-full px-3 text-sm font-medium text-slate-500 ring-1 ring-slate-200">
                        <x-heroicon-o-chevron-left class="h-5 w-5" aria-hidden="true" />
                        <span class="sr-only sm:not-sr-only">{{ __('pagination.previous') }}</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-11 min-w-11 items-center justify-center gap-1 rounded-full px-3 text-sm font-medium text-slate-700 ring-1 ring-slate-300 transition-colors duration-200 hover:bg-slate-100 hover:text-navy-900">
                        <x-heroicon-o-chevron-left class="h-5 w-5" aria-hidden="true" />
                        <span class="sr-only sm:not-sr-only">{{ __('pagination.previous') }}</span>
                    </a>
                @endif
            </li>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span aria-hidden="true" class="inline-flex h-11 min-w-11 items-center justify-center text-sm text-slate-500">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex h-11 min-w-11 items-center justify-center rounded-full bg-navy-900 px-3 text-sm font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="{{ __('pagination.go_to_page', ['page' => $page]) }}" class="inline-flex h-11 min-w-11 items-center justify-center rounded-full px-3 text-sm font-medium text-slate-700 ring-1 ring-slate-300 transition-colors duration-200 hover:bg-slate-100 hover:text-navy-900">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-11 min-w-11 items-center justify-center gap-1 rounded-full px-3 text-sm font-medium text-slate-700 ring-1 ring-slate-300 transition-colors duration-200 hover:bg-slate-100 hover:text-navy-900">
                        <span class="sr-only sm:not-sr-only">{{ __('pagination.next') }}</span>
                        <x-heroicon-o-chevron-right class="h-5 w-5" aria-hidden="true" />
                    </a>
                @else
                    <span class="inline-flex h-11 min-w-11 items-center justify-center gap-1 rounded-full px-3 text-sm font-medium text-slate-500 ring-1 ring-slate-200">
                        <span class="sr-only sm:not-sr-only">{{ __('pagination.next') }}</span>
                        <x-heroicon-o-chevron-right class="h-5 w-5" aria-hidden="true" />
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
