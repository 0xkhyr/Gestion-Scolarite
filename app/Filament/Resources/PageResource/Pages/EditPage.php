<?php

namespace App\Filament\Resources\PageResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetToDefault')
                ->label(__('app.reset_to_default'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('app.reset_to_default_modal_heading'))
                ->modalDescription(__('app.reset_to_default_modal_desc'))
                ->modalSubmitActionLabel(__('app.reset_to_default_confirm'))
                // Only meaningful when the page actually has saved customizations
                ->visible(fn () => ! empty($this->record->settings))
                ->action(function () {
                    $this->record->update(['settings' => []]);
                    $this->fillForm();

                    Notification::make()
                        ->title(__('app.page_reset_to_default'))
                        ->success()
                        ->send();
                }),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
