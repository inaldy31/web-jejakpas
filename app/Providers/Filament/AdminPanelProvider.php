<?php

namespace App\Providers\Filament;

use App\Filament\AvatarProviders\LocalAvatarProvider;
use App\Filament\Pages\Auth\Login;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->strictAuthorization()
            ->brandName(fn (): string => __('jejak.app_name'))
            ->brandLogo(fn () => view('filament.admin.brand-logo'))
            ->darkModeBrandLogo(fn () => view('filament.admin.brand-logo', ['dark' => true]))
            ->brandLogoHeight('2.25rem')
            ->favicon(fn (): string => asset('favicon.ico'))
            ->font(
                'Plus Jakarta Sans',
                url: fn (): string => asset('fonts/plus-jakarta-sans/font.css'),
                provider: LocalFontProvider::class,
                preload: fn (): array => [asset('fonts/plus-jakarta-sans/PlusJakartaSans-Variable.woff2')],
            )
            ->defaultAvatarProvider(LocalAvatarProvider::class)
            ->navigationGroups([
                __('jejak.nav_groups.data_mitra'),
                __('jejak.nav_groups.program_warga_binaan'),
                __('jejak.nav_groups.pemantauan'),
                __('jejak.nav_groups.konten'),
                __('jejak.nav_groups.sistem'),
            ])
            ->colors([
                'primary' => [
                    50 => '#ecf5fd',
                    100 => '#d0e5fb',
                    200 => '#97c7f6',
                    300 => '#55a3f1',
                    400 => '#137fec',
                    500 => '#0f61b3',
                    600 => '#093d71',
                    700 => '#07305a',
                    800 => '#06294b',
                    900 => '#05213d',
                    950 => '#031426',
                ],
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
