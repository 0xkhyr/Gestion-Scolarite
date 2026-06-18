<?php

namespace App\Filament\Resources\EnseignantResource\Pages;

use Filament\Actions\EditAction;
use Illuminate\Support\Collection;
use App\Filament\Resources\EnseignantResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewEnseignant extends ViewRecord
{
    protected static string $resource = EnseignantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        

        $matieresBadges = $this->formatBadges(
            $record->matieres,
            'matieres-badge',
            fn ($matiere) => __('app.' . $matiere->code_matiere),
        );

        $classesBadges = $this->formatBadges(
            $record->classes,
            'classes-badge',
            fn ($classe) => $classe->nom_classe,
        );

        return [
            ...$data,
            'email_display' => $record->user?->email,
            'matieres_list' => filled($matieresBadges) ? $this->getBadgesStyles() . $matieresBadges : null,
            'classes_list' => filled($classesBadges) ? $classesBadges : null,
        ];
    }

    protected function getBadgesStyles(): string
    {
        return <<<'HTML'
<style>
    body.dark .matieres-badge {
        background-color: #1d4ed8 !important;
        color: #e0f2fe !important;
    }
    body.dark .classes-badge {
        background-color: #1d4ed8 !important;
        color: #e0f2fe !important;
    }
</style>
HTML;
    }

    protected function formatBadges(
        Collection $items,
        string $badgeClass,
        callable $labelResolver,
    ): string {
        return $items
            ->map(fn ($item) => sprintf(
                '<span class="%s inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-semibold tracking-wide" style="background-color:#dbeafe; color:#1d4ed8;">%s</span>',
                $badgeClass,
                $labelResolver($item),
            ))
            ->join(' ');
    }
}
