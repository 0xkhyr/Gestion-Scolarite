<?php

namespace App\Filament\Resources\EnseignantResource\Pages;

use App\Models\Enseignant;
use App\Filament\Resources\EnseignantResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEnseignant extends CreateRecord
{
    protected static string $resource = EnseignantResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove user account fields before creating enseignant
        unset($data['email'], $data['password'], $data['is_active']);
        return $data;
    }
    
    protected function afterCreate(): void
    {
        // Create user account only if email was provided
        $email = $this->data['email'] ?? null;
        
        if (filled($email)) {
            $password = $this->data['password'] ?? null;
            
            if (empty($password)) {
                // Default password for teachers
                $password = 'teacher123';
            }
            
            $user = User::create([
                'name' => trim(($this->record->prenom ?? '') . ' ' . ($this->record->nom ?? '')),
                'email' => $email,
                'password' => bcrypt($password),
                'is_active' => $this->data['is_active'] ?? true,
                'profile_type' => Enseignant::class,
                'profile_id' => $this->record->id_enseignant,
            ]);
            
            // Assign teacher role
            $user->assignRole('teacher');
        }
    }
}
