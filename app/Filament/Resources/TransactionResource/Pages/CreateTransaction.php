<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Events\TransactionCreated;


class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['transaction_at'] = now();

        $transationTime = carbon::now('Asia/Jakarta');

        $dateTime = $transationTime->format('ymdHi');
        $queue = str_pad($data['queue_number'], 2, '0', STR_PAD_LEFT);
        $data['invoice'] = 'TRX-' . $dateTime . $queue;

        // Tambahkan nama kasir dari user yang login
        $data['cashier_name'] = Auth::user()->name;

        // Set waiting_at ketika transaksi dibuat dengan status menunggu
        if ($data['status'] === 'menunggu') {
            $data['waiting_at'] = now();
        }

        // Handle customer creation/update dengan increment total_wash
        if (!$data['is_existing_customer']) {
            // Create new customer dengan total_wash = 1
            $customer = Customer::create([
                'name' => $data['customer_name'],
                'sapaan' => $data['customer_sapaan'],
                'phone' => $data['customer_phone'],
                'address' => $data['customer_address'] ?? null,
                'total_wash' => 1, // Pelanggan baru mulai dari 1
                'free_wash_count' => 0,
            ]);
            $data['customer_id'] = $customer->id;
        } else {
            // Update existing customer - increment total_wash
            $customer = Customer::find($data['customer_id']);
            if ($customer) {
                $customer->increment('total_wash');
                if ($data['is_free']) {
                    $customer->increment('free_wash_count');
                }
                // // Optional: Logika untuk free wash (contoh: setiap 10x cuci dapat 1 gratis)
                // if ($customer->total_wash % 10 == 0) {
                //     $customer->increment('free_wash_count');
                // }
            }
        }

        // Remove customer form fields as they're not part of transaction table
        unset($data['customer_name']);
        unset($data['customer_sapaan']);
        unset($data['customer_phone']);
        unset($data['customer_address']);
        unset($data['is_existing_customer']);

        return $data;
    }


    protected function afterCreate(): void
    {
        event(new TransactionCreated($this->record));
        Notification::make()
            ->title('Transaksi berhasil dibuat')
            ->body('Nomor Invoice: ' . $this->record->invoice . ' | Antrian: ' . $this->record->queue_number)
            ->success()
            ->send();
    }
}
