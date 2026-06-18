<?php

namespace App\Filament\Resources\EtudiantResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\EtudiantResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEtudiant extends ViewRecord
{
    protected static string $resource = EtudiantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        return [
            ...$data,
            'email_display' => $record->user?->email,
        ];
    }
}