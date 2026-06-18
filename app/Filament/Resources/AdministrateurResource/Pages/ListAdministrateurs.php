<?php

namespace App\Filament\Resources\AdministrateurResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\AdministrateurResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdministrateurs extends ListRecords
{
    protected static string $resource = AdministrateurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
