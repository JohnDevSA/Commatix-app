<?php

namespace App\Filament\Widgets;

use App\Models\Subscriber;
use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TenantActivityChart extends ChartWidget
{
    protected static ?string $heading = 'Activity Overview';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $tenantId = auth()->user()->tenant_id;
        $months = collect();
        $taskData = [];
        $subscriberData = [];

        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->format('M Y');

            $tasks = Task::where('tenant_id', $tenantId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $subscribers = Subscriber::where('tenant_id', $tenantId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $months->push($monthLabel);
            $taskData[] = $tasks;
            $subscriberData[] = $subscribers;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tasks Created',
                    'data' => $taskData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'New Subscribers',
                    'data' => $subscriberData,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
