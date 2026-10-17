<p {{ $attributes->merge(['class' => 'flex items-start gap-2 text-sm text-slate-600']) }}>
    <x-heroicon-o-lock-closed class="mt-0.5 h-5 w-5 shrink-0 text-slate-500" aria-hidden="true" />
    {{ $slot }}
</p>
