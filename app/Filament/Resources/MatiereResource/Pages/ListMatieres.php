<?php

namespace App\Filament\Resources\MatiereResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\MatiereResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMatieres extends ListRecords
{
    protected static string $resource = MatiereResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
