<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\EvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEvaluation extends ViewRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manage_grades')
                ->label(__('app.saisir_notes'))
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->url(fn () => static::getResource()::getUrl('grades', ['record' => $this->record]))
                ->visible(fn () => auth()->user()->can('grade.manage') || auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'enseignant'])),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
