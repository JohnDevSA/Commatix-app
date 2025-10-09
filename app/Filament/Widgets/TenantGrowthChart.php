<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TenantGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Tenant Growth';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = collect();
        $data = [];

        // Get last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->format('M Y');

            $count = Tenant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $months->push($monthLabel);
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'New Tenants',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
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
