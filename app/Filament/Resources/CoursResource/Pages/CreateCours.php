<?php

namespace App\Filament\Resources\CoursResource\Pages;

use App\Filament\Resources\CoursResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCours extends CreateRecord
{
    protected static string $resource = CoursResource::class;

    /** Recurring slots carry no date; clear any value left by the hidden field. */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['date'])) {
            $data['date'] = null;
        }

        return $data;
    }
}
