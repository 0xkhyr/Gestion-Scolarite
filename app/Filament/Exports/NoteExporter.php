<?php

namespace App\Filament\Exports;

use App\Models\Note;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class NoteExporter extends Exporter
{
    protected static ?string $model = Note::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('etudiant.matricule')->label(__('app.matricule')),
            ExportColumn::make('etudiant.nom')->label(__('app.nom')),
            ExportColumn::make('etudiant.prenom')->label(__('app.prenom')),
            ExportColumn::make('classe.code')->label(__('app.classe')),
            ExportColumn::make('matiere.code_matiere')->label(__('app.matiere')),
            ExportColumn::make('evaluation.titre')->label(__('app.evaluation')),
            ExportColumn::make('type')->label(__('app.type')),
            ExportColumn::make('note')->label(__('app.note')),
            ExportColumn::make('commentaire')->label(__('app.commentaire')),
            ExportColumn::make('created_at')->label(__('app.cree_a')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your note export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
