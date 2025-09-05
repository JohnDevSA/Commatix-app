<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class TenantGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Tenant Growth Over Time';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get data for the last 30 days
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        // Create array of all dates in range
        $dates = collect();
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dates->push($current->copy());
            $current->addDay();
        }

        // Get tenant creation data
        $tenantData = Tenant::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing dates with 0 counts
        $chartData = $dates->map(function ($date) use ($tenantData) {
            $dateString = $date->format('Y-m-d');
            return [
                'date' => $dateString,
                'count' => $tenantData->get($dateString)?->count ?? 0,
                'label' => $date->format('M j')
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'New Tenants',
                    'data' => $chartData->pluck('count')->toArray(),
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ]
            ],
            'labels' => $chartData->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
