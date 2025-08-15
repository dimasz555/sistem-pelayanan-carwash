<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class IncomeChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;
    protected static ?string $heading = 'Grafik Pemasukan';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        Carbon::setLocale('id');

        // Ambil filter dari dashboard
        $startDate = $this->filters['startDate'] ?? now()->startOfMonth();
        $endDate = $this->filters['endDate'] ?? now();

        // Convert to Carbon if string
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Tentukan period berdasarkan rentang tanggal
        $days = $start->diffInDays($end);

        if ($days <= 1) {
            $trendPeriod = 'perHour';
            $labelFormat = 'H:i';
        } elseif ($days <= 31) {
            $trendPeriod = 'perDay';
            $labelFormat = 'd M';
        } elseif ($days <= 93) {
            $trendPeriod = 'perWeek';
            $labelFormat = 'W/Y';
        } else {
            $trendPeriod = 'perMonth';
            $labelFormat = 'M Y';
        }

        // Build query
        $query = Transaction::query()->where('is_paid', true);
        $hasColumn = Schema::hasColumn('transactions', 'total_price');

        if ($hasColumn) {
            // Gunakan Trend jika ada kolom total_price
            $data = Trend::query($query)
                ->between(start: $start, end: $end)
                ->{$trendPeriod}()
                ->sum('total_price');

            $labels = $data->map(function (TrendValue $value) use ($labelFormat, $trendPeriod) {
                return $this->formatLabel($value->date, $trendPeriod, $labelFormat);
            });

            $dataPoints = $data->map(fn(TrendValue $value) => $value->aggregate);
        } else {
            // Hitung manual jika tidak ada kolom total_price
            [$dataPoints, $labels] = $this->getManualData($start, $end, $trendPeriod, $labelFormat);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pemasukan (Rp)',
                    'data' => $dataPoints,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function formatLabel(string $date, string $trendPeriod, string $labelFormat): string
    {
        $carbon = Carbon::parse($date)->setTimezone('Asia/Jakarta');
        Carbon::setLocale('id');

        return match ($trendPeriod) {
            'perHour' => $carbon->format('H:i') . ' WIB',
            'perDay' => $carbon->translatedFormat($labelFormat),
            'perWeek' => 'Minggu ke-' . $carbon->weekOfYear . '/' . $carbon->year,
            'perMonth' => $carbon->translatedFormat($labelFormat),
            default => $carbon->translatedFormat($labelFormat),
        };
    }

    private function getManualData(Carbon $start, Carbon $end, string $trendPeriod, string $labelFormat): array
    {
        $transactions = Transaction::query()
            ->where('is_paid', true)
            ->whereBetween('transaction_at', [$start, $end])
            ->with('service')
            ->get();

        $groupedData = $transactions->groupBy(function ($transaction) use ($trendPeriod) {
            $date = Carbon::parse($transaction->transaction_at)->setTimezone('Asia/Jakarta');
            return match ($trendPeriod) {
                'perHour' => $date->format('Y-m-d H:00:00'),
                'perDay' => $date->format('Y-m-d'),
                'perWeek' => $date->startOfWeek(Carbon::MONDAY)->format('Y-m-d'),
                'perMonth' => $date->format('Y-m'),
                default => $date->format('Y-m-d'),
            };
        });

        $dataPoints = [];
        $labels = [];
        $sortedKeys = collect($groupedData->keys())->sort()->toArray();
        Carbon::setLocale('id');

        foreach ($sortedKeys as $periodKey) {
            $periodTransactions = $groupedData[$periodKey];

            $total = $periodTransactions->sum(function ($transaction) {
                // Cek apakah ada total_price, jika tidak hitung dari service price
                if (isset($transaction->total_price) && $transaction->total_price > 0) {
                    return $transaction->total_price;
                }
                return $transaction->service ? $transaction->service->price : 0;
            });

            $dataPoints[] = $total;

            $labels[] = match ($trendPeriod) {
                'perHour' => Carbon::createFromFormat('Y-m-d H:i:s', $periodKey)
                    ->setTimezone('Asia/Jakarta')
                    ->format('H:i') . ' WIB',
                'perDay' => Carbon::createFromFormat('Y-m-d', $periodKey)
                    ->setTimezone('Asia/Jakarta')
                    ->translatedFormat($labelFormat),
                'perWeek' => 'Minggu ke-' .
                    Carbon::createFromFormat('Y-m-d', $periodKey)->setTimezone('Asia/Jakarta')->weekOfYear .
                    '/' .
                    Carbon::createFromFormat('Y-m-d', $periodKey)->setTimezone('Asia/Jakarta')->year,
                'perMonth' => Carbon::createFromFormat('Y-m', $periodKey)
                    ->setTimezone('Asia/Jakarta')
                    ->translatedFormat($labelFormat),
                default => Carbon::createFromFormat('Y-m-d', $periodKey)
                    ->setTimezone('Asia/Jakarta')
                    ->translatedFormat($labelFormat),
            };
        }

        return [$dataPoints, $labels];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getPollingInterval(): ?string
    {
        return '30s';
    }
}
