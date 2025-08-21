<?php

namespace App\Filament\Pages;

use App\Models\Transaction;
use App\Models\Service;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use App\Filament\Widgets\VehicleStatsWidget;
use App\Filament\Widgets\IncomeChart;
use App\Filament\Widgets\TransactionStatsWidget;
use App\Filament\Widgets\ServiceUsageChart;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public static function canAccess(): bool
    {
        // Semua role bisa akses dashboard
        return Auth::user()->hasAnyRole(['super_admin', 'koordinator', 'kasir']);
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        // Hidden fields
                        \Filament\Forms\Components\Hidden::make('period_type')
                            ->default('weekly'),

                        \Filament\Forms\Components\Hidden::make('period_offset')
                            ->default(0),

                        \Filament\Forms\Components\Hidden::make('startDate')
                            ->default(now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d')),

                        \Filament\Forms\Components\Hidden::make('endDate')
                            ->default(now()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d')),

                        // Layout compact dalam satu baris
                        \Filament\Forms\Components\Grid::make([
                            'default' => 1,
                            'md' => 6,  // Medium dan large sama-sama 1 baris
                            'lg' => 6
                        ])
                            ->schema([
                                // Period Type Buttons - Compact
                                \Filament\Forms\Components\Grid::make(3)
                                    ->schema([
                                        \Filament\Forms\Components\Actions::make([
                                            \Filament\Forms\Components\Actions\Action::make('daily_period')
                                                ->label('Harian')
                                                ->size('sm')
                                                ->color(function ($get) {
                                                    return $get('period_type') === 'daily' ? 'primary' : 'gray';
                                                })
                                                ->outlined(function ($get) {
                                                    return $get('period_type') !== 'daily';
                                                })
                                                ->action(function ($set, $get) {
                                                    $set('period_type', 'daily');
                                                    $set('period_offset', 0);
                                                    $this->updateDatesFromOffset($set, 'daily', 0);
                                                }),
                                        ])->fullWidth(),

                                        \Filament\Forms\Components\Actions::make([
                                            \Filament\Forms\Components\Actions\Action::make('weekly_period')
                                                ->label('Mingguan')
                                                ->size('sm')
                                                ->color(function ($get) {
                                                    return $get('period_type') === 'weekly' ? 'primary' : 'gray';
                                                })
                                                ->outlined(function ($get) {
                                                    return $get('period_type') !== 'weekly';
                                                })
                                                ->action(function ($set, $get) {
                                                    $set('period_type', 'weekly');
                                                    $set('period_offset', 0);
                                                    $this->updateDatesFromOffset($set, 'weekly', 0);
                                                }),
                                        ])->fullWidth(),

                                        \Filament\Forms\Components\Actions::make([
                                            \Filament\Forms\Components\Actions\Action::make('monthly_period')
                                                ->label('Bulanan')
                                                ->size('sm')
                                                ->color(function ($get) {
                                                    return $get('period_type') === 'monthly' ? 'primary' : 'gray';
                                                })
                                                ->outlined(function ($get) {
                                                    return $get('period_type') !== 'monthly';
                                                })
                                                ->action(function ($set, $get) {
                                                    $set('period_type', 'monthly');
                                                    $set('period_offset', 0);
                                                    $this->updateDatesFromOffset($set, 'monthly', 0);
                                                }),
                                        ])->fullWidth(),
                                    ])
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 3,  // Medium: 3 kolom dari 6
                                        'lg' => 3
                                    ])
                                    ->extraAttributes(['class' => 'gap-1']),

                                // Navigation dan Current Period - Compact untuk mobile
                                \Filament\Forms\Components\Grid::make([
                                    'default' => 3,  // Mobile: 3 kolom untuk prev, current, next
                                    'md' => 3,       // MD: 3 kolom dalam 1 baris
                                    'lg' => 3        // LG: tetap 3 kolom
                                ])
                                    ->schema([
                                        // Navigation Previous
                                        \Filament\Forms\Components\Actions::make([
                                            \Filament\Forms\Components\Actions\Action::make('previous')
                                                ->icon('heroicon-o-chevron-left')
                                                ->iconButton()
                                                ->size('sm')
                                                ->color('gray')
                                                ->action(function ($set, $get) {
                                                    $periodType = $get('period_type') ?? 'weekly';
                                                    $currentOffset = $get('period_offset') ?? 0;
                                                    $newOffset = $currentOffset - 1;
                                                    $set('period_offset', $newOffset);
                                                    $this->updateDatesFromOffset($set, $periodType, $newOffset);
                                                }),
                                        ])
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex justify-center items-center']),

                                        // Current Period Display
                                        \Filament\Forms\Components\Placeholder::make('current_period')
                                            ->label('')
                                            ->content(function ($get) {
                                                $startDate = $get('startDate');
                                                $endDate = $get('endDate');
                                                $periodType = $get('period_type') ?? 'weekly';

                                                if (!$startDate || !$endDate) {
                                                    return 'Pilih periode';
                                                }

                                                $start = Carbon::parse($startDate);
                                                $end = Carbon::parse($endDate);
                                                Carbon::setLocale('id');

                                                if ($periodType === 'daily') {
                                                    return $start->translatedFormat('d M Y');
                                                } elseif ($periodType === 'weekly') {
                                                    return $start->translatedFormat('d M') . ' - ' . $end->translatedFormat('d M Y');
                                                } else { // monthly
                                                    return $start->translatedFormat('M Y');
                                                }
                                            })
                                            ->columnSpan(1)
                                            ->extraAttributes([
                                                'class' => 'text-center font-medium text-gray-900 dark:text-gray-100 flex items-center justify-center min-h-[32px] px-1 text-sm md:text-base'
                                            ]),

                                        // Navigation Next
                                        \Filament\Forms\Components\Actions::make([
                                            \Filament\Forms\Components\Actions\Action::make('next')
                                                ->icon('heroicon-o-chevron-right')
                                                ->iconButton()
                                                ->size('sm')
                                                ->color('gray')
                                                ->disabled(function ($get) {
                                                    $endDate = $get('endDate');
                                                    if (!$endDate) return false;
                                                    return Carbon::parse($endDate)->isAfter(now());
                                                })
                                                ->action(function ($set, $get) {
                                                    $periodType = $get('period_type') ?? 'weekly';
                                                    $currentOffset = $get('period_offset') ?? 0;
                                                    $newOffset = $currentOffset + 1;
                                                    $set('period_offset', $newOffset);
                                                    $this->updateDatesFromOffset($set, $periodType, $newOffset);
                                                }),
                                        ])
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex justify-center items-center']),
                                    ])
                                    ->columnSpan([
                                        'default' => 1,  // Mobile: full width di bawah buttons
                                        'md' => 3,       // MD: 3 kolom dari 6, dalam 1 baris
                                        'lg' => 3        // LG: 3 kolom dari 6
                                    ])
                                    ->extraAttributes(['class' => 'gap-1']),
                            ])
                            ->extraAttributes(['class' => 'items-center gap-1']), // Ubah gap dari 2 ke 1
                    ])
                    ->extraAttributes([
                        'class' => 'bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-3 shadow-sm'
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private function updateDatesFromOffset($set, $periodType, $offset)
    {
        $now = now();

        switch ($periodType) {
            case 'daily':
                $targetDate = $now->copy()->addDays($offset);
                $set('startDate', $targetDate->format('Y-m-d'));
                $set('endDate', $targetDate->format('Y-m-d'));
                break;

            case 'weekly':
                $targetWeek = $now->copy()->addWeeks($offset);
                $set('startDate', $targetWeek->startOfWeek(Carbon::MONDAY)->format('Y-m-d'));
                $set('endDate', $targetWeek->endOfWeek(Carbon::SUNDAY)->format('Y-m-d'));
                break;

            case 'monthly':
                $targetMonth = $now->copy()->addMonths($offset);
                $set('startDate', $targetMonth->startOfMonth()->format('Y-m-d'));
                $set('endDate', $targetMonth->endOfMonth()->format('Y-m-d'));
                break;
        }
    }

    public function getWidgets(): array
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return [
                VehicleStatsWidget::class,
                TransactionStatsWidget::class,
                IncomeChart::class,
                ServiceUsageChart::class,
            ];
        } elseif ($user->hasRole('koordinator')) {
            return [
                VehicleStatsWidget::class,
                ServiceUsageChart::class,
            ];
        } elseif ($user->hasRole('kasir')) {
            return [
                TransactionStatsWidget::class,
                IncomeChart::class,
            ];
        }

        return [];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}
