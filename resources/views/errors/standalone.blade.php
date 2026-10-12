<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ __('jejak.app_name') }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; box-sizing: border-box; background: #f8fafc; color: #1d293d; font-family: ui-sans-serif, system-ui, sans-serif; line-height: 1.6; }
        main { max-width: 32rem; text-align: center; }
        .brand { margin: 0; font-size: 0.875rem; font-weight: 800; letter-spacing: 0.2em; color: #05213d; }
        h1 { margin: 0.75rem 0 0; font-size: 1.75rem; line-height: 1.25; color: #05213d; }
        p { margin: 0.75rem 0 0; color: #45556c; }
        a { display: inline-block; margin-top: 1.5rem; padding: 0.75rem 1.25rem; border-radius: 9999px; background: #05213d; color: #fff; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
    <main>
        <p class="brand">{{ __('jejak.app_name') }}</p>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        @yield('action')
    </main>
</body>
</html>
