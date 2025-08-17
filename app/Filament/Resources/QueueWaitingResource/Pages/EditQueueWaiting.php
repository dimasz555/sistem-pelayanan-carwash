<?php

namespace App\Filament\Resources\QueueWaitingResource\Pages;

use App\Filament\Resources\QueueWaitingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQueueWaiting extends EditRecord
{
    protected static string $resource = QueueWaitingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
