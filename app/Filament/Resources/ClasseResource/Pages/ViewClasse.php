<?php

namespace App\Filament\Resources\ClasseResource\Pages;

use App\Filament\Resources\ClasseResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClasse extends ViewRecord
{
    protected static string $resource = ClasseResource::class;

    public function getTitle(): string
    {
        return $this->record->code;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view-timetable')
                ->label(__('app.emploi_temps'))
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->url(fn (): string => ClasseResource::getUrl('view-timetable', ['record' => $this->record])),

            EditAction::make(),
        ];
    }
}
