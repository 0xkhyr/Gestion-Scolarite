<?php

namespace App\Filament\Resources\AdministrateurResource\Pages;

use App\Models\Administrateur;
use App\Filament\Resources\AdministrateurResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAdministrateur extends CreateRecord
{
    protected static string $resource = AdministrateurResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove user account fields before creating administrateur
        unset($data['email'], $data['password'], $data['is_active'], $data['two_factor_enabled'], $data['role']);
        return $data;
    }
    
    protected function afterCreate(): void
    {
        // Create user account for this administrator
        $user = User::create([
            'name' => trim(($this->record->prenom ?? '') . ' ' . ($this->record->nom ?? '')),
            'email' => $this->data['email'],
            'password' => bcrypt($this->data['password']),
            'is_active' => $this->data['is_active'] ?? true,
            'two_factor_enabled' => $this->data['two_factor_enabled'] ?? false,
            'profile_type' => Administrateur::class,
            'profile_id' => $this->record->id_administrateur,
        ]);
        
        // Assign the role selected in the form
        $roleFromForm = $this->data['role'] ?? 'admin';
        $user->assignRole($roleFromForm);
    }
}
