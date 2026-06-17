<?php

namespace App\Filament\Resources\AdministrateurResource\Pages;

use App\Filament\Resources\AdministrateurResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdministrateur extends EditRecord
{
    protected static string $resource = AdministrateurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('resetPassword')
                ->label(__('app.reset_password'))
                ->icon('heroicon-o-key')
                ->color('warning')
                ->visible(fn () => auth()->user()->hasPermissionTo('user.manage'))
                ->modalHeading(__('app.reset_password'))
                ->modalSubmitActionLabel(__('app.reset_password'))
                ->form(AdministrateurResource::passwordResetFormSchema())
                ->action(fn (array $data) => AdministrateurResource::applyPasswordReset($this->record, $data)),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load user account data if exists
        $user = User::where('profile_type', \App\Models\Administrateur::class)
            ->where('profile_id', $this->record->id_administrateur)
            ->first();
        
        if ($user) {
            $data['email'] = $user->email;
            $data['is_active'] = $user->is_active;
            $data['two_factor_enabled'] = $user->two_factor_enabled ?? false;
            $data['role'] = $user->getRoleNames()->first() ?? 'admin';
        }
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove user account fields before saving administrateur
        unset($data['email'], $data['password'], $data['is_active'], $data['two_factor_enabled'], $data['role']);
        return $data;
    }
    
    protected function afterSave(): void
    {
        // Find the user associated with this administrator
        $user = User::where('profile_type', \App\Models\Administrateur::class)
            ->where('profile_id', $this->record->id_administrateur)
            ->first();
        
        if ($user) {
            // Update user data
            $userData = [
                'name' => trim(($this->record->prenom ?? '') . ' ' . ($this->record->nom ?? '')),
                'email' => $this->data['email'],
                'is_active' => $this->data['is_active'] ?? true,
                'two_factor_enabled' => $this->data['two_factor_enabled'] ?? false,
            ];
            
            // Update password only if provided
            if (!empty($this->data['password'])) {
                $userData['password'] = bcrypt($this->data['password']);
            }
            
            $user->update($userData);
            
            // Sync the role from the form
            $roleFromForm = $this->data['role'] ?? 'admin';
            $user->syncRoles([$roleFromForm]);
        }
    }
}
