<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class TransactionStatsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Ambil filter dari dashboard
        $startDate = $this->filters['startDate'] ?? now()->startOfMonth();
        $endDate = $this->filters['endDate'] ?? now();

        // Convert to Carbon if string
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Query untuk periode yang dipilih
        $baseQuery = Transaction::whereBetween('transaction_at', [$start, $end]);

        // Total Transaksi
        $totalTransactions = $baseQuery->clone()->count();

        // Total Uang Yang Sudah Dibayar (hanya hitung yang tidak gratis)
        $paidTransactions = $baseQuery->clone()
            ->where('is_paid', true)
            ->where('is_free', false) // Exclude transaksi gratis
            ->get();

        $totalPaid = $paidTransactions->sum('total_price');

        // Total Uang Yang Belum Dibayar (hanya hitung yang tidak gratis)
        $unpaidTransactions = $baseQuery->clone()
            ->where('is_paid', false)
            ->where('is_free', false) // Exclude transaksi gratis
            ->get();

        $totalUnpaid = $unpaidTransactions->sum('total_price');

        // Total Uang (Paid + Unpaid)
        $totalAmount = $totalPaid + $totalUnpaid;

        // Hitung jumlah transaksi gratis untuk informasi
        $freeTransactions = $baseQuery->clone()
            ->where('is_free', true)
            ->count();

        // Hitung persentase dibanding periode sebelumnya untuk trend
        $previousPeriod = $start->diffInDays($end);
        $previousStart = $start->copy()->subDays($previousPeriod + 1);
        $previousEnd = $start->copy()->subDay();

        $previousQuery = Transaction::whereBetween('transaction_at', [$previousStart, $previousEnd]);

        $previousTotalTransactions = $previousQuery->clone()->count();
        $previousTotalPaid = $previousQuery->clone()
            ->where('is_paid', true)
            ->where('is_free', false)
            ->sum('total_price');
        $previousTotalUnpaid = $previousQuery->clone()
            ->where('is_paid', false)
            ->where('is_free', false)
            ->sum('total_price');
        $previousTotalAmount = $previousTotalPaid + $previousTotalUnpaid;

        // Hitung persentase perubahan
        $transactionChange = $previousTotalTransactions > 0
            ? (($totalTransactions - $previousTotalTransactions) / $previousTotalTransactions) * 100
            : 0;

        $paidChange = $previousTotalPaid > 0
            ? (($totalPaid - $previousTotalPaid) / $previousTotalPaid) * 100
            : 0;

        $totalAmountChange = $previousTotalAmount > 0
            ? (($totalAmount - $previousTotalAmount) / $previousTotalAmount) * 100
            : 0;

        return [
            Stat::make('Total Transaksi', number_format($totalTransactions))
                ->description(
                    ($transactionChange >= 0 ? 'Naik ' . number_format(abs($transactionChange), 1) . '%' : 'Turun ' . number_format(abs($transactionChange), 1) . '%') .
                        ($freeTransactions > 0 ? ' • ' . $freeTransactions . ' gratis' : '')
                )
                ->descriptionIcon($transactionChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($transactionChange >= 0 ? 'success' : 'danger')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Total Transaksi (Rp)', 'Rp ' . number_format($totalAmount))
                ->description($totalAmountChange >= 0 ? 'Naik ' . number_format(abs($totalAmountChange), 1) . '%' : 'Turun ' . number_format(abs($totalAmountChange), 1) . '%')
                ->descriptionIcon($totalAmountChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($totalAmountChange >= 0 ? 'success' : 'danger')
                ->chart([12, 8, 14, 10, 18, 12, 20]),

            Stat::make('Uang Sudah Dibayar', 'Rp ' . number_format($totalPaid))
                ->description($paidChange >= 0 ? 'Naik ' . number_format(abs($paidChange), 1) . '%' : 'Turun ' . number_format(abs($paidChange), 1) . '%')
                ->descriptionIcon($paidChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($paidChange >= 0 ? 'success' : 'danger')
                ->chart([17, 16, 14, 15, 14, 13, 12]),

            Stat::make('Uang Belum Dibayar', 'Rp ' . number_format($totalUnpaid))
                ->description('Transaksi yang belum dibayar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([15, 4, 10, 2, 12, 4, 12]),
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '30s';
    }
}
