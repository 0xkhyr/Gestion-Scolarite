<?php

namespace App\Filament\Resources\AdminAllowedIpResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\AdminAllowedIpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminAllowedIp extends EditRecord
{
    protected static string $resource = AdminAllowedIpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
