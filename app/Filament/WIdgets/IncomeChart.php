<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;

class IncomeChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Grafik Pemasukan';

    public ?string $filter = 'this_month';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari Ini',
            'yesterday' => 'Kemarin',
            'this_week' => 'Minggu Ini',
            'last_week' => 'Minggu Lalu',
            'this_month' => 'Bulan Ini',
            'last_month' => 'Bulan Lalu',
            // 'this_quarter' => '3 Bulan Terakhir',
            'this_year' => 'Tahun Ini',
            'last_year' => 'Tahun Lalu',
            'last_30_days' => '30 Hari Terakhir',
            // 'last_90_days' => '90 Hari Terakhir',
        ];
    }

    protected function getData(): array
    {
        // Set locale Indonesia untuk seluruh widget
        Carbon::setLocale('id');

        // Ambil filter dari widget internal
        $period = $this->filter ?? 'this_month';

        // Tentukan rentang tanggal berdasarkan periode yang dipilih
        [$start, $end, $trendPeriod, $labelFormat] = $this->getPeriodRange($period);

        // Build query dengan pengecekan kolom
        $query = Transaction::query()->where('is_paid', true);
        $hasColumn = Schema::hasColumn('transactions', 'total_price');

        if ($hasColumn) {
            // Jika ada kolom total_price, gunakan Trend
            $data = Trend::query($query)
                ->between(start: $start, end: $end)
                ->{$trendPeriod}()
                ->sum('total_price');

            $labels = $data->map(function (TrendValue $value) use ($labelFormat, $trendPeriod) {
                return $this->formatLabel($value->date, $trendPeriod, $labelFormat);
            });

            $dataPoints = $data->map(fn(TrendValue $value) => $value->aggregate);
        } else {
            // Jika tidak ada kolom total_price, hitung manual
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

    private function getPeriodRange(string $period): array
    {
        // Gunakan timezone Jakarta
        $now = now('Asia/Jakarta');

        return match ($period) {
            'today' => [
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
                'perHour',
                'H:i'
            ],
            'yesterday' => [
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->subDay()->endOfDay(),
                'perHour',
                'H:i'
            ],
            'this_week' => [
                $now->copy()->startOfWeek(Carbon::MONDAY), // Mulai dari Senin
                $now->copy()->endOfWeek(Carbon::SUNDAY),   // Sampai Minggu
                'perDay',
                'D, d M'
            ],
            'last_week' => [
                $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY),
                $now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY),
                'perDay',
                'D, d M'
            ],
            'this_month' => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
                'perDay',
                'd M'
            ],
            'last_month' => [
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth(),
                'perDay',
                'd M'
            ],
            // 'this_quarter' => [
            //     $now->copy()->startOfQuarter(),
            //     $now->copy()->endOfQuarter(),
            //     'perWeek',
            //     'W/Y'
            // ],
            'this_year' => [
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear(),
                'perMonth',
                'M Y'
            ],
            'last_year' => [
                $now->copy()->subYear()->startOfYear(),
                $now->copy()->subYear()->endOfYear(),
                'perMonth',
                'M Y'
            ],
            'last_30_days' => [
                $now->copy()->subDays(30),
                $now->copy(),
                'perDay',
                'd M'
            ],
            // 'last_90_days' => [
            //     $now->copy()->subDays(90),
            //     $now->copy(),
            //     'perWeek',
            //     'W/Y'
            // ],
            default => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
                'perDay',
                'd M'
            ],
        };
    }

    private function formatLabel(string $date, string $trendPeriod, string $labelFormat): string
    {
        // Convert ke timezone Jakarta dan set locale Indonesia
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
            // Convert ke timezone Jakarta
            $date = Carbon::parse($transaction->transaction_at)->setTimezone('Asia/Jakarta');
            return match ($trendPeriod) {
                'perHour' => $date->format('Y-m-d H:00:00'),
                'perDay' => $date->format('Y-m-d'),
                'perWeek' => $date->startOfWeek(Carbon::MONDAY)->format('Y-m-d'), // Gunakan tanggal awal minggu
                'perMonth' => $date->format('Y-m'),
                default => $date->format('Y-m-d'),
            };
        });

        $dataPoints = [];
        $labels = [];

        // Sort keys untuk urutan yang benar
        $sortedKeys = collect($groupedData->keys())->sort()->toArray();

        // Set locale Indonesia
        Carbon::setLocale('id');

        foreach ($sortedKeys as $periodKey) {
            $periodTransactions = $groupedData[$periodKey];

            $total = $periodTransactions->sum(function ($transaction) {
                return $transaction->service ? $transaction->service->price : 0;
            });

            $dataPoints[] = $total;

            // Format label dengan timezone Jakarta
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
