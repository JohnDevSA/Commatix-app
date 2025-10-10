<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SystemOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $trialTenants = Tenant::where('status', 'trial')->count();
        $totalUsers = User::count();

        // Calculate subscription revenue (if you have subscription data)
        $subscriptionRevenue = TenantSubscription::where('status', 'active')
            ->sum('amount') ?? 0;

        $tenantsGrowth = $this->calculateTenantsGrowth();
        $revenueGrowth = $this->calculateRevenueGrowth();
        $userGrowth = $this->calculateUserGrowth();

        return [
            Stat::make('Total Tenants', number_format($totalTenants))
                ->description($tenantsGrowth['description'])
                ->descriptionIcon($tenantsGrowth['icon'])
                ->color($tenantsGrowth['color'])
                ->chart($this->getTenantChart())
                ->extraAttributes([
                    'class' => 'metric-card animate-metric-up',
                ]),

            Stat::make('Active Tenants', number_format($activeTenants))
                ->description("{$trialTenants} on trial")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->extraAttributes([
                    'class' => 'metric-card animate-metric-up',
                    'style' => 'animation-delay: 0.1s',
                ]),

            Stat::make('Total Users', number_format($totalUsers))
                ->description($userGrowth['description'])
                ->descriptionIcon($userGrowth['icon'])
                ->color($userGrowth['color'])
                ->chart($this->getUserChart())
                ->extraAttributes([
                    'class' => 'metric-card animate-metric-up',
                    'style' => 'animation-delay: 0.2s',
                ]),

            Stat::make('Monthly Revenue', 'R' . number_format($subscriptionRevenue, 2))
                ->description($revenueGrowth['description'])
                ->descriptionIcon($revenueGrowth['icon'])
                ->color($revenueGrowth['color'])
                ->extraAttributes([
                    'class' => 'metric-card animate-metric-up',
                    'style' => 'animation-delay: 0.3s',
                ]),

            Stat::make('System Health', '99.8%')
                ->description('Uptime this month')
                ->descriptionIcon('heroicon-m-heart')
                ->color('success')
                ->extraAttributes([
                    'class' => 'metric-card animate-metric-up',
                    'style' => 'animation-delay: 0.4s',
                ]),

            Stat::make('Storage Used', $this->getStorageUsed())
                ->description('Total across all tenants')
                ->descriptionIcon('heroicon-m-server')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'metric-card animate-metric-up',
                    'style' => 'animation-delay: 0.5s',
                ]),
        ];
    }

    private function calculateTenantsGrowth(): array
    {
        $currentMonth = Tenant::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $lastMonth = Tenant::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($lastMonth == 0) {
            return [
                'description' => "{$currentMonth} new this month",
                'icon' => 'heroicon-m-arrow-trending-up',
                'color' => 'success',
            ];
        }

        $growth = (($currentMonth - $lastMonth) / $lastMonth) * 100;

        return [
            'description' => abs(round($growth, 1)) . '% ' . ($growth >= 0 ? 'increase' : 'decrease'),
            'icon' => $growth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down',
            'color' => $growth >= 0 ? 'success' : 'danger',
        ];
    }

    private function calculateUserGrowth(): array
    {
        $currentMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $lastMonth = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($lastMonth == 0) {
            return [
                'description' => "{$currentMonth} new this month",
                'icon' => 'heroicon-m-arrow-trending-up',
                'color' => 'success',
            ];
        }

        $growth = (($currentMonth - $lastMonth) / $lastMonth) * 100;

        return [
            'description' => abs(round($growth, 1)) . '% ' . ($growth >= 0 ? 'increase' : 'decrease'),
            'icon' => $growth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down',
            'color' => $growth >= 0 ? 'success' : 'danger',
        ];
    }

    private function calculateRevenueGrowth(): array
    {
        $currentRevenue = TenantSubscription::where('status', 'active')
            ->whereMonth('current_period_start', now()->month)
            ->sum('amount') ?? 0;

        $lastMonthRevenue = TenantSubscription::where('status', 'active')
            ->whereMonth('current_period_start', now()->subMonth()->month)
            ->sum('amount') ?? 0;

        if ($lastMonthRevenue == 0) {
            return [
                'description' => 'No previous data',
                'icon' => 'heroicon-m-minus',
                'color' => 'gray',
            ];
        }

        $growth = (($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;

        return [
            'description' => abs(round($growth, 1)) . '% ' . ($growth >= 0 ? 'increase' : 'decrease'),
            'icon' => $growth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down',
            'color' => $growth >= 0 ? 'success' : 'danger',
        ];
    }

    private function getTenantChart(): array
    {
        return collect(range(6, 0))
            ->map(fn ($daysAgo) => Tenant::whereDate('created_at', now()->subDays($daysAgo))->count())
            ->toArray();
    }

    private function getUserChart(): array
    {
        return collect(range(6, 0))
            ->map(fn ($daysAgo) => User::whereDate('created_at', now()->subDays($daysAgo))->count())
            ->toArray();
    }

    private function getStorageUsed(): string
    {
        // Check if TenantUsage table exists and has data
        try {
            $totalMB = DB::table('tenant_usages')->sum('storage_used_mb') ?? 0;
            if ($totalMB > 1024) {
                return number_format($totalMB / 1024, 2) . ' GB';
            }

            return number_format($totalMB, 0) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}
