<?php

namespace App\Filament\Resources\QueueProcessingResource\Pages;

use App\Filament\Resources\QueueProcessingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQueueProcessings extends ListRecords
{
    protected static string $resource = QueueProcessingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
