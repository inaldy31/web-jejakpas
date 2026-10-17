<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    private const PERMISSIONS_POLICY = 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = $response->headers;
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'DENY');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', self::PERMISSIONS_POLICY);
        $headers->set(
            config('jejak.security.csp_report_only') ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy',
            $this->contentSecurityPolicy($request),
        );

        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function contentSecurityPolicy(Request $request): string
    {
        $scripts = ["'self'", ...($this->isAdmin($request) ? ["'unsafe-inline'"] : []), "'unsafe-eval'"];
        $styles = ["'self'", "'unsafe-inline'"];
        $fonts = ["'self'"];
        $connect = ["'self'"];

        if (is_file($hot = (string) Vite::hotFile()) && ($origin = rtrim(trim((string) file_get_contents($hot)), '/')) !== '') {
            $scripts[] = $origin;
            $styles[] = $origin;
            $fonts[] = $origin;
            $connect[] = preg_replace('#^http#', 'ws', $origin);
        }

        return implode('; ', [
            "default-src 'self'",
            'script-src '.implode(' ', $scripts),
            'style-src '.implode(' ', $styles),
            "img-src 'self' data:",
            'font-src '.implode(' ', $fonts),
            'connect-src '.implode(' ', $connect),
            "form-action 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
        ]);
    }

    private function isAdmin(Request $request): bool
    {
        $path = trim(Filament::getDefaultPanel()->getPath(), '/');

        return $request->is($path, "{$path}/*");
    }
}
