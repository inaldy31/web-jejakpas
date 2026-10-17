<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Filament::auth()->user();

        if (
            $user instanceof User
            && $user->must_change_password
            && ! $request->routeIs('filament.*.auth.password-change', 'filament.*.auth.logout')
        ) {
            $url = route('filament.'.Filament::getCurrentOrDefaultPanel()->getId().'.auth.password-change');

            return $request->isMethod('GET') && ! $request->expectsJson() && ! $request->headers->has('X-Livewire')
                ? redirect()->guest($url)
                : redirect()->to($url);
        }

        return $next($request);
    }
}
