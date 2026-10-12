@props(['dark' => false])

<span style="display: flex; align-items: center; gap: 0.5rem; height: 100%;">
    <span style="display: flex; align-items: center; gap: 0.25rem; height: 100%;">
        <img src="{{ asset('images/lambang-imipas.png') }}" alt="{{ __('jejak.logos.ministry') }}" title="{{ __('jejak.ministry') }}" width="128" height="128" style="height: 100%; width: auto;">
        <img src="{{ asset('images/lambang-pemasyarakatan.png') }}" alt="{{ __('jejak.logos.corrections') }}" width="135" height="128" style="height: 100%; width: auto;">
    </span>
    <span style="font-weight: 800; font-size: 1.125rem; line-height: 1;">
        <span style="color: {{ $dark ? '#ffffff' : '#05213d' }};">JEJAK</span>
        <span style="color: #f1bd58;">PAS</span>
    </span>
</span>
