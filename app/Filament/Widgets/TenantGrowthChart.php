<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class TenantGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Tenant Growth Analytics';
    protected static ?string $description = 'Real-time tenant acquisition tracking across South African markets';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '350px';
    protected static ?string $pollingInterval = '60s';

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

        // Get cumulative data
        $cumulativeData = Tenant::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $runningTotal = 0;
        $cumulativeCounts = collect();

        // Fill in missing dates with 0 counts
        $chartData = $dates->map(function ($date) use ($tenantData, &$runningTotal, $cumulativeData) {
            $dateString = $date->format('Y-m-d');
            $dailyCount = $tenantData->get($dateString)?->count ?? 0;
            $runningTotal += $dailyCount;
            
            return [
                'date' => $dateString,
                'count' => $dailyCount,
                'cumulative' => $runningTotal,
                'label' => $date->format('M j')
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'New Tenants (Daily)',
                    'data' => $chartData->pluck('count')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(59, 130, 246)',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => 'Total Tenants (Cumulative)',
                    'data' => $chartData->pluck('cumulative')->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.05)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                    'fill' => false,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(16, 185, 129)',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 3,
                    'yAxisID' => 'y1',
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
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'font' => [
                            'size' => 12,
                            'weight' => '500',
                        ],
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(255, 255, 255, 0.95)',
                    'titleColor' => '#1f2937',
                    'bodyColor' => '#1f2937',
                    'borderColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderWidth' => 1,
                    'cornerRadius' => 8,
                    'displayColors' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.05)',
                        'borderColor' => 'rgba(0, 0, 0, 0.1)',
                    ],
                    'ticks' => [
                        'color' => '#6b7280',
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.05)',
                        'borderColor' => 'rgba(0, 0, 0, 0.1)',
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                        'color' => '#6b7280',
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'New Tenants',
                        'color' => '#374151',
                        'font' => [
                            'size' => 12,
                            'weight' => '600',
                        ],
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'beginAtZero' => true,
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'ticks' => [
                        'color' => '#6b7280',
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Total Tenants',
                        'color' => '#374151',
                        'font' => [
                            'size' => 12,
                            'weight' => '600',
                        ],
                    ],
                ],
            ],
        ];
    }
}