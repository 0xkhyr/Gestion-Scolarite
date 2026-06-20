<?php

namespace App\Filament\Resources\ClasseResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ClasseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClasses extends ListRecords
{
    protected static string $resource = ClasseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
