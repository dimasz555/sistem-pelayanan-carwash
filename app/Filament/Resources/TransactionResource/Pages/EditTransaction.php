<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Customer;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $customer = $this->record->customer;

        if ($customer) {
            $data['customer_phone'] = $customer->phone;
            $data['customer_name'] = $customer->name;
            $data['customer_sapaan'] = $customer->sapaan;
            $data['customer_address'] = $customer->address;
            $data['is_existing_customer'] = true;
        }

        return $data;
    }
}
