<?php

namespace App\Filament\Resources\QueueCompletedResource\Pages;

use App\Filament\Resources\QueueCompletedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQueueCompleted extends EditRecord
{
    protected static string $resource = QueueCompletedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
