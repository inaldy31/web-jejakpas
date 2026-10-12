@props(['status'])

@php
    $admin = request()->is('admin', 'admin/*', 'admin-berkas/*');
    $primary = 'inline-flex h-11 items-center rounded-full bg-navy-900 px-5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-navy-800';
    $secondary = 'inline-flex h-11 items-center rounded-full px-4 text-sm font-medium text-slate-600 transition-colors duration-200 hover:bg-slate-100 hover:text-navy-900';
@endphp

<x-layouts.public :title="__('jejak.errors.'.$status.'.title')">
    <x-page-hero :eyebrow="__('jejak.errors.eyebrow', ['status' => $status])">
        <x-slot:title>{{ __('jejak.errors.'.$status.'.title') }}</x-slot:title>
        <x-slot:lead>{{ __('jejak.errors.'.$status.'.message') }}</x-slot:lead>
    </x-page-hero>

    <div class="mx-auto mt-10 max-w-7xl px-4">
        <x-empty-state :title="__('jejak.errors.help')">
            <x-slot:action>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    @if ($admin)
                        <a href="{{ route('filament.admin.pages.dashboard') }}" class="{{ $primary }}">{{ __('jejak.errors.admin') }}</a>
                        <a href="{{ route('home') }}" class="{{ $secondary }}">{{ __('jejak.errors.home') }}</a>
                    @else
                        <a href="{{ route('home') }}" class="{{ $primary }}">{{ __('jejak.errors.home') }}</a>
                        <a href="{{ route('partners.index') }}" class="{{ $secondary }}">{{ __('jejak.errors.partners') }}</a>
                        <a href="{{ route('knowledge.index') }}" class="{{ $secondary }}">{{ __('jejak.errors.knowledge') }}</a>
                    @endif
                </div>
            </x-slot:action>
        </x-empty-state>
    </div>
</x-layouts.public>
