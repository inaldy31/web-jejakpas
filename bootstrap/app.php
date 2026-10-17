<?php

use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\TrustHosts;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        // Pemeriksaan kesehatan untuk pemantauan server, berupa teks polos tanpa sesi: halaman `health: '/up'` bawaan Laravel
        // memuat font dan skrip dari CDN, yang diblokir CSP dan tercatat oleh pemindai keamanan.
        then: function (): void {
            Route::get('/up', fn () => response('OK', 200, ['Content-Type' => 'text/plain; charset=UTF-8']))->name('health');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/admin/login');
        // Paling luar, supaya respons yang dijawab middleware global sendiri (400 jalur tidak sah atau host asing, 413, 503
        // pemeliharaan) juga berheader; TrustProxies tepat di dalamnya, supaya HSTS pada respons itu pun tahu request lewat proxy
        // TLS. Lalu TrustHosts: di server (bukan APP_ENV=local) hanya host APP_URL dan subdomainnya yang dijawab, dan host lain
        // sudah mendapat 400 sebelum middleware global bawaan Laravel sempat menjawabnya dengan tautan atau redirect ke host itu.
        $middleware->prepend([SecurityHeaders::class, TrustProxies::class, TrustHosts::class]);
        // Dari proxy tepercaya hanya alamat klien dan skema yang dipakai; host, port, dan awalan URL tetap dari request itu sendiri,
        // jadi header X-Forwarded-Host/-Port/-Prefix kiriman klien yang diteruskan proxy tidak bisa membelokkan redirect dan tautan.
        $middleware->trustProxies(headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO);
        // Seperti rute kesehatan bawaan Laravel, /up tetap menjawab OK selama `php artisan down`; halaman lain tetap 503.
        $middleware->preventRequestsDuringMaintenance(except: ['up']);
        // Laravel mengurutkan Authenticate ke depan; tanpa ini akun nonaktif mendapat 403 dari Authenticate panel alih-alih dikeluarkan.
        $middleware->prependToPriorityList(before: AuthenticatesRequests::class, prepend: EnsureAccountIsActive::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
