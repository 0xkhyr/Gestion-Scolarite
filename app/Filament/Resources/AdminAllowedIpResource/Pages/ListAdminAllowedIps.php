<?php

namespace App\Filament\Resources\AdminAllowedIpResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\AdminAllowedIpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminAllowedIps extends ListRecords
{
    protected static string $resource = AdminAllowedIpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label(__('app.ajouter_ip')),
        ];
    }
}
