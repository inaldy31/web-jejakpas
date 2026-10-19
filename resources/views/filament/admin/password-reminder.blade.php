<x-filament::callout role="status" color="warning" icon="heroicon-o-key" :heading="__('jejak.account.password_reminder')" style="margin-top: 2rem;">
    <x-slot name="footer">
        <x-filament::link :href="filament()->getProfileUrl()" color="warning">{{ __('jejak.account.password_reminder_link') }}</x-filament::link>
    </x-slot>
</x-filament::callout>
