<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

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
            Actions\Action::make('take')
                ->label(__('app.take_attendance'))
                ->icon('heroicon-o-clipboard-document-check')
                ->url(fn () => \App\Filament\Pages\TakeAttendance::getUrl())
                ->visible(fn () => \App\Filament\Pages\TakeAttendance::canAccess()),
        ];
    }
}
