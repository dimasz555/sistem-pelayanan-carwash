<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\Service;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ServiceUsageChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;
    protected static ?string $heading = 'Grafik Layanan Yang Digunakan';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Ambil filter dari dashboard
        $startDate = $this->filters['startDate'] ?? now()->startOfMonth();
        $endDate = $this->filters['endDate'] ?? now();

        // Convert to Carbon if string
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Ambil data layanan yang paling banyak digunakan
        $serviceUsage = Transaction::query()
            ->whereBetween('transaction_at', [$start, $end])
            ->whereNotNull('service_id')
            ->with('service')
            ->select('service_id', DB::raw('COUNT(*) as usage_count'))
            ->groupBy('service_id')
            ->orderBy('usage_count', 'desc')
            ->limit(10) // Ambil 10 layanan teratas
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [
            'rgba(255, 99, 132, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 205, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(199, 199, 199, 0.8)',
            'rgba(83, 102, 255, 0.8)',
            'rgba(255, 99, 255, 0.8)',
            'rgba(99, 255, 132, 0.8)',
        ];

        $borderColors = [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 205, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(199, 199, 199, 1)',
            'rgba(83, 102, 255, 1)',
            'rgba(255, 99, 255, 1)',
            'rgba(99, 255, 132, 1)',
        ];

        foreach ($serviceUsage as $index => $usage) {
            $serviceName = $usage->service ? $usage->service->name : 'Layanan Tidak Diketahui';
            $labels[] = strlen($serviceName) > 20 ? substr($serviceName, 0, 20) . '...' : $serviceName;
            $data[] = $usage->usage_count;
        }

        // Jika tidak ada data
        if (empty($data)) {
            return [
                'datasets' => [
                    [
                        'label' => 'Jumlah Penggunaan',
                        'data' => [1],
                        'backgroundColor' => ['rgba(156, 163, 175, 0.8)'],
                        'borderColor' => ['rgba(156, 163, 175, 1)'],
                        'borderWidth' => 1,
                    ],
                ],
                'labels' => ['Tidak ada data'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Penggunaan',
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($data)),
                    'borderColor' => array_slice($borderColors, 0, count($data)),
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
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
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.label + ": " + context.parsed + " transaksi (" + 
                                   Math.round((context.parsed / context.dataset.data.reduce((a, b) => a + b, 0)) * 100) + "%)";
                        }',
                    ],
                ],
            ],
            'cutout' => '50%',
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '30s';
    }
}
