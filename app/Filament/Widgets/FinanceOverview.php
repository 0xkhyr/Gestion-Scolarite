<?php

namespace App\Filament\Widgets;

use App\Models\EtudePaiement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceOverview extends BaseWidget
{
    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin', 'director', 'accountant']);
    }

    protected function getStats(): array
    {
        $collected = (float) EtudePaiement::where('statut', 'paye')->sum('montant');
        $partial = (float) EtudePaiement::where('statut', 'partiel')->sum('montant');
        $outstanding = (float) EtudePaiement::where('statut', 'non_paye')->sum('montant');

        return [
            Stat::make(__('app.fees_collected'), $this->money($collected))
                ->description(__('app.paye'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make(__('app.fees_partial'), $this->money($partial))
                ->description(__('app.partiel'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('app.fees_outstanding'), $this->money($outstanding))
                ->description(__('app.non_paye'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }

    private function money(float $amount): string
    {
        return number_format($amount, 0, '.', ' ') . ' MRU';
    }
}
