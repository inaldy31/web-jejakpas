<x-layouts.public :title="__('jejak.footer.terms')">
    <x-page-hero :eyebrow="__('jejak.app_name')" width="3xl">
        <x-slot:title>{{ __('jejak.footer.terms') }}</x-slot:title>
    </x-page-hero>

    <article class="mx-auto mt-10 max-w-3xl space-y-4 px-4 text-slate-700">
        <p>{{ __('jejak.pages.terms.info') }}</p>
        <p>{{ __('jejak.pages.terms.documents') }}</p>
        <p>{{ __('jejak.pages.terms.usage') }}</p>
    </article>
</x-layouts.public>
