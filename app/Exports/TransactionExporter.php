<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransactionExporter implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithMapping
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

            // Jika query adalah Query Builder, tambahkan orderBy
            return $this->query->orderBy('transaction_at', 'asc')->get();
        }

        return Transaction::with(['customer', 'service.category', 'service.size'])
            ->orderBy('transaction_at', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Waktu Transaksi',
            'Invoice',
            'Nama Pelanggan',
            'Layanan',
            'Kategori',
            'Jenis Kendaraan',
            'Total Harga',
            'Plat Nomor',
            'Nama Kendaraan',
            'Status',
            'Pencucian Gratis',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_at
                ? \Carbon\Carbon::parse($transaction->transaction_at)
                ->locale('id')
                ->timezone('Asia/Jakarta')
                ->isoFormat('dddd, DD/MM/YYYY, HH:mm')
                : '',
            $transaction->invoice,
            $transaction->customer->name ?? '',
            $transaction->service->name ?? '',
            $transaction->service->category->name ?? '',
            $transaction->service->size->name ?? '',
            'Rp ' . number_format($transaction->total_price, 0, ',', '.'),
            $transaction->plate_number,
            $transaction->vehicle_name,
            match ($transaction->status) {
                'menunggu' => 'Menunggu',
                'proses' => 'Proses',
                'selesai' => 'Selesai',
                default => 'Tidak Diketahui',
            },
            $transaction->is_free ? 'Ya' : 'Tidak',
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
            'A' => 30, // Waktu Transaksi
            'B' => 20, // Invoice
            'C' => 25, // Nama Pelanggan
            'D' => 25, // Layanan
            'E' => 20, // Kategori
            'F' => 20, // Jenis Kendaraan
            'G' => 20, // Total Harga
            'H' => 18, // Plat Nomor
            'I' => 25, // Nama Kendaraan
            'J' => 15, // Status
            'K' => 18, // Pencucian Gratis
        ];
    }
}
