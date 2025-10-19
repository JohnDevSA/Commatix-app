<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\RecentTenantsWidget;
use App\Filament\Widgets\SystemOverviewWidget;
use App\Filament\Widgets\TenantGrowthChart;
use App\Http\Middleware\RedirectIfNotOnboarded;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Stephenjude\FilamentDebugger\DebuggerPlugin;
use Filament\Navigation\NavigationItem;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('dashboard')
            ->login()
            ->registration()
            ->brandName('Commatix')
//            ->brandLogo(asset('images/commatix-logo.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('images/favicon.ico'))
            ->colors([
                // Commatix OKLCH Primary Brand Colors
                'primary' => [
                    50 => 'oklch(0.98 0.02 200)',
                    100 => 'oklch(0.95 0.05 200)',
                    200 => 'oklch(0.90 0.08 200)',
                    300 => 'oklch(0.82 0.12 200)',
                    400 => 'oklch(0.74 0.15 200)',
                    500 => 'oklch(0.65 0.18 200)',  // Main brand color
                    600 => 'oklch(0.56 0.15 200)',
                    700 => 'oklch(0.47 0.12 200)',
                    800 => 'oklch(0.38 0.09 200)',
                    900 => 'oklch(0.29 0.06 200)',
                    950 => 'oklch(0.20 0.03 200)',
                ],
                // South African Gold Accent
                'sa-gold' => [
                    500 => 'oklch(0.8 0.12 85)',
                ],
                // Semantic colors (keep standard for consistency)
                'secondary' => Color::Slate,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Red,
            ])
            ->font('Figtree')
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17rem')
            ->renderHook(
                'panels::body.start',
                fn () => view('filament.custom-styles')
            )
            ->renderHook(
                'panels::head.end',
                fn () => view('filament.themes.glass')
            )
            ->renderHook(
                'panels::styles.before',
                fn () => auth()->check() && auth()->user()->tenant
                    ? view('filament.tenant-colors', ['tenant' => auth()->user()->tenant])
                    : ''
            )
            ->navigationGroups([
                'Dashboard',
                'Multi-Tenant Management',
                'Communication Hub',
                'Workflow Engine',
                'Analytics & Reports',
                'System Administration',
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                SystemOverviewWidget::class,
                TenantGrowthChart::class,
                RecentTenantsWidget::class,
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                RedirectIfNotOnboarded::class,
            ])
            ->plugins([
                DebuggerPlugin::make()
                    ->authorize(fn () => app()->environment('local') || auth()->user()?->hasRole('super_admin'))
                    ->telescopeNavigation(condition: false) // Disable default navigation items
                    ->pulseNavigation(condition: false)
                    ->horizonNavigation(condition: false),

                EnvironmentIndicatorPlugin::make()
                    ->visible(fn () => app()->environment('local') || app()->environment('staging'))
                    ->showBadge(true)
                    ->color(fn () => match (app()->environment()) {
                        'local' => Color::Green,
                        'staging' => Color::Amber,
                        'production' => Color::Red,
                        default => Color::Gray,
                    }),
            ])
            ->navigationItems([
                NavigationItem::make('Telescope')
                    ->url('/telescope', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-magnifying-glass-circle')
                    ->group('Debugger')
                    ->sort(1)
                    ->badge('Available', 'success')
                    ->badgeTooltip('Opens Laravel Telescope debugger in new tab - Track requests, queries, jobs, and exceptions')
                    ->visible(fn () => app()->environment('local') || auth()->user()?->hasRole('super_admin')),

                NavigationItem::make('Pulse')
                    ->url('/pulse', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-chart-bar')
                    ->group('Debugger')
                    ->sort(2)
                    ->badge('Available', 'success')
                    ->badgeTooltip('Opens Laravel Pulse dashboard in new tab - Real-time performance monitoring')
                    ->visible(fn () => app()->environment('local') || auth()->user()?->hasRole('super_admin')),

                NavigationItem::make('Horizon')
                    ->url('#')
                    ->icon('heroicon-o-queue-list')
                    ->group('Debugger')
                    ->sort(3)
                    ->badge('Q1 2025', 'warning')
                    ->badgeTooltip('Laravel Horizon - Coming Q1 2025 (Dependency conflict with PHP 8.4)')
                    ->visible(fn () => app()->environment('local') || auth()->user()?->hasRole('super_admin')),
            ]);
    }
}
