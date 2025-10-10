<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\RecentTenantsWidget;
use App\Filament\Widgets\SystemOverviewWidget;
use App\Filament\Widgets\TenantGrowthChart;
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
            ->path('/')
            ->login()
            ->brandName('Commatix')
//            ->brandLogo(asset('images/commatix-logo.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('images/favicon.ico'))
            ->colors([
                'primary' => Color::Blue,
                'secondary' => Color::Slate,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Red,
            ])
            ->font('Figtree')
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17rem')
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
