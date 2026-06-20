<?php

namespace App\Filament\Exports;

use App\Models\EtudePaiement;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EtudePaiementExporter extends Exporter
{
    protected static ?string $model = EtudePaiement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('etudiant.matricule')->label(__('app.matricule')),
            ExportColumn::make('etudiant.nom')->label(__('app.nom')),
            ExportColumn::make('etudiant.prenom')->label(__('app.prenom')),
            ExportColumn::make('typepaye')->label(__('app.type')),
            ExportColumn::make('montant')->label(__('app.montant')),
            ExportColumn::make('statut')->label(__('app.statut')),
            ExportColumn::make('date_paiement')->label(__('app.payment_date')),
            ExportColumn::make('created_at')->label(__('app.cree_a')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your etude paiement export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
