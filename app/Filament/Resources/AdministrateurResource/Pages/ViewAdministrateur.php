<?php

namespace App\Filament\Resources\AdministrateurResource\Pages;

use App\Filament\Resources\AdministrateurResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAdministrateur extends ViewRecord
{
    protected static string $resource = AdministrateurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        // Format recovery codes if they exist
        $recoveryCodes = null;
        if ($record->user?->two_factor_recovery_codes) {
            $codes = json_decode(decrypt($record->user->two_factor_recovery_codes), true);
            if (is_array($codes)) {
                $recoveryCodes = implode("\n", $codes);
            }
        }

        return [
            ...$data,
            'email_display' => $record->user?->email,
            'two_factor_recovery_codes' => $recoveryCodes,
        ];
    }
}
