<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Models\User;
use App\Models\Subscriber;
use App\Models\TenantUsage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $tenantId = auth()->user()->tenant_id;

        // Tenant-specific metrics
        $totalUsers = User::where('tenant_id', $tenantId)->count();
        $activeUsers = $totalUsers; // All users are considered active

        $totalTasks = Task::where('tenant_id', $tenantId)->count();
        $activeTasks = Task::where('tenant_id', $tenantId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
        $completedTasks = Task::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->count();

        $totalSubscribers = Subscriber::where('tenant_id', $tenantId)->count();
        $activeSubscribers = Subscriber::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->count();

        // Get credit balance if available
        $tenant = auth()->user()->tenant;
        $creditBalance = $tenant->credit_balance ?? 0;

        // Get usage data
        $usage = TenantUsage::where('tenant_id', $tenantId)->latest()->first();
        $storageUsed = $usage ? $usage->storage_used_mb : 0;

        $userGrowth = $this->calculateUserGrowth($tenantId);
        $taskGrowth = $this->calculateTaskGrowth($tenantId);
        $subscriberGrowth = $this->calculateSubscriberGrowth($tenantId);

        return [
            Stat::make('Active Tasks', number_format($activeTasks))
                ->description("{$completedTasks} completed, {$totalTasks} total")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart($this->getTaskChart($tenantId)),

            Stat::make('Team Members', number_format($activeUsers))
                ->description($userGrowth['description'])
                ->descriptionIcon($userGrowth['icon'])
                ->color($userGrowth['color'])
                ->chart($this->getUserChart($tenantId)),

            Stat::make('Active Subscribers', number_format($activeSubscribers))
                ->description($subscriberGrowth['description'])
                ->descriptionIcon($subscriberGrowth['icon'])
                ->color($subscriberGrowth['color'])
                ->chart($this->getSubscriberChart($tenantId)),

            Stat::make('Credit Balance', number_format($creditBalance))
                ->description('Available credits')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($creditBalance > 100 ? 'success' : 'warning'),

            Stat::make('Storage Used', $this->formatStorage($storageUsed))
                ->description('Document storage')
                ->descriptionIcon('heroicon-m-server')
                ->color('info'),

            Stat::make('Task Completion', $this->getCompletionRate($tenantId))
                ->description($taskGrowth['description'])
                ->descriptionIcon($taskGrowth['icon'])
                ->color($taskGrowth['color']),
        ];
    }

    private function calculateUserGrowth(int|string $tenantId): array
    {
        $currentMonth = User::where('tenant_id', $tenantId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if ($currentMonth == 0) {
            return [
                'description' => "No new members this month",
                'icon' => 'heroicon-m-minus',
                'color' => 'gray'
            ];
        }

        return [
            'description' => "{$currentMonth} new this month",
            'icon' => 'heroicon-m-arrow-trending-up',
            'color' => 'success'
        ];
    }

    private function calculateTaskGrowth(int|string $tenantId): array
    {
        $completedThisMonth = Task::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        return [
            'description' => "{$completedThisMonth} completed this month",
            'icon' => 'heroicon-m-check-circle',
            'color' => 'success'
        ];
    }

    private function calculateSubscriberGrowth(int|string $tenantId): array
    {
        $currentMonth = Subscriber::where('tenant_id', $tenantId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if ($currentMonth == 0) {
            return [
                'description' => "No new subscribers this month",
                'icon' => 'heroicon-m-minus',
                'color' => 'gray'
            ];
        }

        return [
            'description' => "{$currentMonth} new this month",
            'icon' => 'heroicon-m-arrow-trending-up',
            'color' => 'success'
        ];
    }

    private function getTaskChart(int|string $tenantId): array
    {
        return collect(range(6, 0))
            ->map(fn($daysAgo) => Task::where('tenant_id', $tenantId)
                ->whereDate('created_at', now()->subDays($daysAgo))
                ->count())
            ->toArray();
    }

    private function getUserChart(int|string $tenantId): array
    {
        return collect(range(6, 0))
            ->map(fn($daysAgo) => User::where('tenant_id', $tenantId)
                ->whereDate('created_at', now()->subDays($daysAgo))
                ->count())
            ->toArray();
    }

    private function getSubscriberChart(int|string $tenantId): array
    {
        return collect(range(6, 0))
            ->map(fn($daysAgo) => Subscriber::where('tenant_id', $tenantId)
                ->whereDate('created_at', now()->subDays($daysAgo))
                ->count())
            ->toArray();
    }

    private function formatStorage(float|int $mb): string
    {
        if ($mb > 1024) {
            return number_format($mb / 1024, 2) . ' GB';
        }
        return number_format($mb, 0) . ' MB';
    }

    private function getCompletionRate(int|string $tenantId): string
    {
        $total = Task::where('tenant_id', $tenantId)->count();
        if ($total == 0) {
            return '0%';
        }

        $completed = Task::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->count();

        return number_format(($completed / $total) * 100, 1) . '%';
    }
}