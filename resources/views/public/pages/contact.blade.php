@php
    use App\Support\PhoneNumber;
    use Illuminate\Support\Str;

    $address = Str::trim((string) config('jejak.contact.address'));
    $email = Str::trim((string) config('jejak.contact.email'));
    $phone = Str::trim((string) config('jejak.contact.phone'));
    $call = PhoneNumber::firstIn($phone);
@endphp

<x-layouts.public :title="__('jejak.footer.contact')">
    <x-page-hero :eyebrow="__('jejak.app_name')" width="3xl">
        <x-slot:title>{{ __('jejak.footer.contact') }}</x-slot:title>
        <x-slot:lead>{{ __('jejak.pages.contact.intro') }}</x-slot:lead>
    </x-page-hero>

    <article class="mx-auto mt-10 max-w-3xl px-4 text-slate-700">
        <div class="space-y-2 rounded-2xl bg-white p-6 shadow-soft ring-1 ring-slate-900/5">
            <p class="font-semibold text-navy-900">{{ config('jejak.contact.unit') }}</p>
            @if ($address)
                <p>{{ __('jejak.pages.contact.address') }}: {{ $address }}</p>
            @endif
            @if ($email)
                <p>{{ __('jejak.pages.contact.email') }}: <a href="mailto:{{ $email }}" class="font-medium text-navy-700 underline underline-offset-2 [overflow-wrap:anywhere] hover:text-navy-900">{{ $email }}</a></p>
            @endif
            @if ($phone)
                <p>{{ __('jejak.pages.contact.phone') }}: @if ($call){{ $call['before'] }}<a href="tel:{{ $call['dial'] }}" class="font-medium text-navy-700 underline underline-offset-2 hover:text-navy-900">{{ $call['number'] }}</a>{{ $call['after'] }}@else{{ $phone }}@endif</p>
            @endif
            @if (! $address && ! $email && ! $phone)
                <p>{{ __('jejak.pages.contact.pending') }}</p>
            @endif
        </div>
    </article>
</x-layouts.public>
