<?php

namespace App\Filament\Resources\ClasseResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ClasseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClasse extends EditRecord
{
    protected static string $resource = ClasseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
