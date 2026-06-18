<?php

namespace App\Filament\Resources\EtudiantResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\EtudiantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEtudiants extends ListRecords
{
    protected static string $resource = EtudiantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
