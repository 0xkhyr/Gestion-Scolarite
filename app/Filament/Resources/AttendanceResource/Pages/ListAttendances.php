<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use Filament\Actions\Action;
use App\Filament\Pages\TakeAttendance;
use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Records are created via the Take Attendance page.
            Action::make('take')
                ->label(__('app.take_attendance'))
                ->icon('heroicon-o-clipboard-document-check')
                ->url(fn () => TakeAttendance::getUrl())
                ->visible(fn () => TakeAttendance::canAccess()),
        ];
    }
}
