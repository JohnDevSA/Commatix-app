<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentTenantsWidget;
use App\Filament\Widgets\SystemOverviewWidget;
use App\Filament\Widgets\TenantActivityChart;
use App\Filament\Widgets\TenantGrowthChart;
use App\Filament\Widgets\TenantOverviewWidget;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        $user = auth()->user();

        // Super admin sees system-wide metrics
        if ($user->isSuperAdmin()) {
            return [
                SystemOverviewWidget::class,
                TenantGrowthChart::class,
                RecentTenantsWidget::class,
            ];
        }

        // Tenant admin sees tenant-specific metrics
        if ($user->isTenantAdmin()) {
            return [
                TenantOverviewWidget::class,
                TenantActivityChart::class,
            ];
        }

        // Regular users see tenant overview
        return [
            TenantOverviewWidget::class,
            TenantActivityChart::class,
        ];
    }
}
