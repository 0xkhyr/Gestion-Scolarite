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

    /** Recurring slots carry no date; clear any value left by the hidden field. */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['date'])) {
            $data['date'] = null;
        }

        return $data;
    }
}
