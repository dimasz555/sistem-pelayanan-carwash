<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CustomerExporter implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithMapping
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function collection()
    {
        if ($this->query) {
            // Jika query adalah Collection, langsung return
            if ($this->query instanceof \Illuminate\Support\Collection) {
                return $this->query;
            }
        }

        return Customer::query()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Sapaan',
            'Nama Pelanggan',
            'Nomor WhatsApp',
            'Alamat',
            'Total Pencucian',
            'Total Pencucian Gratis',
        ];
    }

    public function map($customer): array
    {
        return [
            // $transaction->transaction_at
            //     ? \Carbon\Carbon::parse($transaction->transaction_at)
            //     ->locale('id')
            //     ->timezone('Asia/Jakarta')
            //     ->isoFormat('dddd, DD/MM/YYYY, HH:mm')
            //     : '',
            $customer->sapaan,
            $customer->name ?? '',
            $customer->phone ?? '',
            $customer->address ?? '',
            $customer->total_wash ?? '',
            $customer->free_wash_count ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Style untuk seluruh tabel - Border hitam
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'], // Hitam
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Style khusus untuk header (baris 1) - Bold
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Set tinggi baris untuk header
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Set tinggi baris untuk data
        for ($row = 2; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(25);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10, 
            'B' => 25, 
            'C' => 20,
            'D' => 25, 
            'E' => 20, 
            'F' => 20,
        ];
    }
}
