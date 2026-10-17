@if (request()->getHost() === '')
    @include('errors::untrusted-host')
@else
    <x-error-page :status="$exception->getStatusCode()" texts="4xx" />
@endif
