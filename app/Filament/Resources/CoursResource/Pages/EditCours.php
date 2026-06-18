<?php

namespace App\Filament\Resources\CoursResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\CoursResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCours extends EditRecord
{
    protected static string $resource = CoursResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
