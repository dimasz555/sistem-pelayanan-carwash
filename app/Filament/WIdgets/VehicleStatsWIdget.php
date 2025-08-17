<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Support\Colors\Color;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class VehicleStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;
    

    protected function getStats(): array
    {
        $start = $this->filters['startDate']
            ? Carbon::parse($this->filters['startDate'])->startOfDay()
            : now()->startOfDay();

        $end = $this->filters['endDate']
            ? Carbon::parse($this->filters['endDate'])->endOfDay()
            : now()->endOfDay();

        // Total kendaraan
        $totalKendaraan = Transaction::whereBetween('transaction_at', [$start, $end])->count();

        // Hitung mobil dengan query terpisah
        $totalMobil = Transaction::whereBetween('transaction_at', [$start, $end])
            ->whereHas('service', function (Builder $query) {
                $query->whereHas('category', function (Builder $q) {
                    $q->where('name', 'LIKE', '%mobil%')
                        ->orWhere('name', 'LIKE', '%Mobil%')
                        ->orWhere('name', 'Mobil');
                });
            })
            ->count();

        // Hitung motor dengan query terpisah
        $totalMotor = Transaction::whereBetween('transaction_at', [$start, $end])
            ->whereHas('service', function (Builder $query) {
                $query->whereHas('category', function (Builder $q) {
                    $q->where('name', 'LIKE', '%motor%')
                        ->orWhere('name', 'LIKE', '%Motor%')
                        ->orWhere('name', 'Motor');
                });
            })
            ->count();

        // Hitung omset - cek apakah kolom total_price ada
        $omset = 0;
        $hasColumn = Schema::hasColumn('transactions', 'total_price');

        if ($hasColumn) {
            $omset = Transaction::whereBetween('transaction_at', [$start, $end])
                ->where('is_paid', true)
                ->sum('total_price');
        }

        // Jika tidak ada total_price atau nilainya 0, gunakan price dari service
        if (!$hasColumn || $omset == 0) {
            $transactions = Transaction::whereBetween('transaction_at', [$start, $end])
                ->where('is_paid', true)
                ->with('service')
                ->get();

            $omset = $transactions->sum(function ($transaction) {
                return $transaction->service ? $transaction->service->price : 0;
            });
        }

        return [
            Stat::make('Total Kendaraan Masuk', number_format($totalKendaraan))
                ->description('Total semua kendaraan yang masuk')
                ->descriptionIcon('heroicon-m-queue-list')
                ->color('primary'),

            Stat::make('Total Mobil', number_format($totalMobil))
                ->description('Total kendaraan mobil')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            Stat::make('Total Motor', number_format($totalMotor))
                ->description('Total kendaraan motor')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('warning'),

            Stat::make('Total Omset', 'Rp ' . number_format($omset, 0, ',', '.'))
                ->description('Total pendapatan periode ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '30s';
    }
}
