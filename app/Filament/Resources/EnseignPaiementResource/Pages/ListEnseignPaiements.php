<?php

namespace App\Filament\Resources\EnseignPaiementResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\EnseignPaiementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEnseignPaiements extends ListRecords
{
    protected static string $resource = EnseignPaiementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
