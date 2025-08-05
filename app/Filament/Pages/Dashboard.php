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
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('super_admin');
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Statistik Kendaraan')
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Tanggal Mulai')
                            ->default(now()->format('Y-m-d'))
                            ->maxDate(fn(Get $get) => $get('endDate') ?: now()->format('Y-m-d'))
                            ->live(),
                        DatePicker::make('endDate')
                            ->label('Tanggal Akhir')
                            ->default(now()->format('Y-m-d'))
                            ->minDate(fn(Get $get) => $get('startDate') ?: now()->format('Y-m-d'))
                            ->maxDate(now()->format('Y-m-d'))
                            ->live(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            VehicleStatsWidget::class,
            IncomeChart::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1;
    }
}
