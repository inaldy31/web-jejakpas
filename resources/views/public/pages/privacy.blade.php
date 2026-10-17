<x-layouts.public :title="__('jejak.footer.privacy')">
    <x-page-hero :eyebrow="__('jejak.app_name')" width="3xl">
        <x-slot:title>{{ __('jejak.footer.privacy') }}</x-slot:title>
    </x-page-hero>

    <article class="mx-auto mt-10 max-w-3xl space-y-4 px-4 text-slate-700">
        <p>{{ __('jejak.pages.privacy.intro', ['unit' => config('jejak.contact.unit')]) }}</p>
        <h2 class="pt-2 text-xl font-bold text-navy-900">{{ __('jejak.pages.privacy.displayed_title') }}</h2>
        <p>{{ __('jejak.pages.privacy.displayed_body') }}</p>
        <p>{{ __('jejak.pages.privacy.threshold', ['count' => (int) config('jejak.privacy.min_group_size')]) }}</p>
        <h2 class="pt-2 text-xl font-bold text-navy-900">{{ __('jejak.pages.privacy.managed_title') }}</h2>
        <p>{{ __('jejak.pages.privacy.managed_body') }}</p>
        <h2 class="pt-2 text-xl font-bold text-navy-900">{{ __('jejak.pages.privacy.cookies_title') }}</h2>
        <p>{{ __('jejak.pages.privacy.cookies_body', ['minutes' => (int) config('session.lifetime')]) }}</p>
        <h2 class="pt-2 text-xl font-bold text-navy-900">{{ __('jejak.footer.contact') }}</h2>
        <p>{{ __('jejak.pages.privacy.contact_before') }} <a href="{{ route('contact') }}" class="font-medium text-navy-700 underline underline-offset-2 hover:text-navy-900">{{ __('jejak.footer.contact') }}</a>.</p>
    </article>
</x-layouts.public>
