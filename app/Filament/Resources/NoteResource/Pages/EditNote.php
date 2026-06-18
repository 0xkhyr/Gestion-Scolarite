<?php

namespace App\Filament\Resources\NoteResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\NoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNote extends EditRecord
{
    protected static string $resource = NoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $ignoreId = $this->getRecord()?->getKey();

        NoteResource::ensureUniqueNoteCombination($data, $ignoreId);

        return NoteResource::fillRequiredEvaluationReferences($data);
    }
}
