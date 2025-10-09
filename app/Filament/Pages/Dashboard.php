<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SystemOverviewWidget;
use App\Filament\Widgets\TenantGrowthChart;
use App\Filament\Widgets\RecentTenantsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            SystemOverviewWidget::class,
            TenantGrowthChart::class,
            RecentTenantsWidget::class,
        ];
    }
}