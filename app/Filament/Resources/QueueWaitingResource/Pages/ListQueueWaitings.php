<?php

namespace App\Filament\Resources\QueueWaitingResource\Pages;

use App\Filament\Resources\QueueWaitingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQueueWaitings extends ListRecords
{
    protected static string $resource = QueueWaitingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
