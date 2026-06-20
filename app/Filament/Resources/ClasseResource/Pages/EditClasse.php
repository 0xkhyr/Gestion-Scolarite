<?php

namespace App\Filament\Resources\ClasseResource\Pages;

use App\Filament\Resources\ClasseResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditClasse extends EditRecord
{
    protected static string $resource = ClasseResource::class;

    public function getTitle(): string
    {
        return __('app.modifier') . ' — ' . $this->record->code;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),

            Action::make('view-timetable')
                ->label(__('app.emploi_temps'))
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->url(fn (): string => ClasseResource::getUrl('view-timetable', ['record' => $this->record])),

            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
