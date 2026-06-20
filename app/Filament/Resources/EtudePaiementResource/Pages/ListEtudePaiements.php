<?php

namespace App\Filament\Resources\EtudePaiementResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\EtudePaiementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEtudePaiements extends ListRecords
{
    protected static string $resource = EtudePaiementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
