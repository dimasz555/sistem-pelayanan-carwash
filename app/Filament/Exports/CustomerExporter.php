<?php

namespace App\Filament\Exports;

use App\Models\Customer;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CustomerExporter extends Exporter
{
    protected static ?string $model = Customer::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nama'),
            ExportColumn::make('phone')->label('Nomor WhatsApp'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export data pelanggan sudah selesai, Silahkan unduh file tersebut. ';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' Gagal melakukan export data.';
        }

        return $body;
    }
}
