<?php

namespace App\Filament\Resources\WhatsappApiTokenResource\Pages;

use App\Filament\Resources\WhatsappApiTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWhatsappApiToken extends EditRecord
{
    protected static string $resource = WhatsappApiTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
