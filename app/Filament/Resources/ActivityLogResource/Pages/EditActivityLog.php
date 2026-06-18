<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ActivityLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityLog extends EditRecord
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
