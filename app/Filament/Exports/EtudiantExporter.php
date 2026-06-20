<?php

namespace App\Filament\Exports;

use App\Models\Etudiant;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EtudiantExporter extends Exporter
{
    protected static ?string $model = Etudiant::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('matricule')->label(__('app.matricule')),
            ExportColumn::make('nom')->label(__('app.nom')),
            ExportColumn::make('prenom')->label(__('app.prenom')),
            ExportColumn::make('genre')->label(__('app.genre')),
            ExportColumn::make('date_naissance')->label(__('app.date_naissance')),
            ExportColumn::make('telephone')->label(__('app.telephone')),
            ExportColumn::make('adresse')->label(__('app.adresse')),
            ExportColumn::make('classe.code')->label(__('app.classe')),
            ExportColumn::make('created_at')->label(__('app.cree_a')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your etudiant export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
