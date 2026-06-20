<?php

namespace App\Filament\Resources\EtudiantResource\Pages;

use App\Models\Etudiant;
use App\Filament\Resources\EtudiantResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEtudiant extends CreateRecord
{
    protected static string $resource = EtudiantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove user-related fields from student data
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
                // If email provided but no password, use matricule as password
                $password = $this->record->matricule;
            }
            
            $user = User::create([
                'email' => $email,
                'password' => bcrypt($password),
                'is_active' => $this->data['is_active'] ?? true,
                'profile_type' => Etudiant::class,
                'profile_id' => $this->record->id_etudiant,
            ]);
            
            // Assign student role
            $user->assignRole('student');
        }
    }
}
