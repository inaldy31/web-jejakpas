<?php

namespace App\Providers;

use App\Support\TrustedDevices;
use Closure;
use DateTime;
use Filament\Forms\Components\DatePicker;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::defaultView('pagination.jejak');

        Password::defaults(fn (): Password => Password::min(8));

        RateLimiter::for('knowledge-download', fn (Request $request): Limit => Limit::perMinute(30)->by($request->ip()));

        DatePicker::configureUsing(function (DatePicker $component): void {
            $component->mutateStateForValidationUsing(fn (mixed $state): mixed => is_array($state) ? 'bukan-tanggal' : $state);

            $component->rule(function (DatePicker $component): Closure {
                $format = $component->getFormat();

                return function (string $attribute, mixed $value, Closure $fail) use ($format): void {
                    if (blank($value)) {
                        return;
                    }

                    $date = is_string($value) ? DateTime::createFromFormat('!'.$format, $value) : false;

                    if (! $date || $date->format($format) !== $value) {
                        $fail(__('jejak.validation.date_invalid_format'));
                    }
                };
            });
        });

        Event::listen(Login::class, function (Login $event): void {
            activity('auth')->causedBy($event->user)->event('login')
                ->withProperties(TrustedDevices::wasUsed(request()) ? ['trusted_device' => true] : [])
                ->log('login');
        });

        Event::listen(Logout::class, function (Logout $event): void {
            if ($event->user !== null) {
                activity('auth')->causedBy($event->user)->event('logout')->log('logout');
            }
        });

        Event::listen(Failed::class, function (Failed $event): void {
            activity('auth')->event('login_failed')->withProperties([
                'username' => $event->credentials['username'] ?? null,
                'ip' => request()->ip(),
            ])->log('login_failed');
        });
    }
}
