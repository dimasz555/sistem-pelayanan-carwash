<?php

namespace App\Filament\Resources\QueueCompletedResource\Pages;

use App\Filament\Resources\QueueCompletedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQueueCompleteds extends ListRecords
{
    protected static string $resource = QueueCompletedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
