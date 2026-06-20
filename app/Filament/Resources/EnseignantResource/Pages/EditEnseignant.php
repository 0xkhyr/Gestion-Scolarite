<?php

namespace App\Filament\Resources\EnseignantResource\Pages;

use Filament\Actions\DeleteAction;
use App\Models\Enseignant;
use App\Filament\Resources\EnseignantResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\Rule;

class EditEnseignant extends EditRecord
{
    protected static string $resource = EnseignantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    
    public function getRules(): array
    {
        $rules = parent::getRules();
        
        // Add custom unique validation for email
        if (isset($rules['email'])) {
            $currentUserId = $this->record->user?->id;
            
            $rules['email'] = array_merge(
                is_array($rules['email']) ? $rules['email'] : [$rules['email']],
                [
                    'nullable',
                    Rule::unique('users', 'email')->ignore($currentUserId),
                ]
            );
        }
        
        return $rules;
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load user account data if exists
        $user = User::where('profile_type', Enseignant::class)
            ->where('profile_id', $this->record->id_enseignant)
            ->first();
        
        if ($user) {
            $data['email'] = $user->email;
            $data['is_active'] = $user->is_active;
        }
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove user account fields before saving enseignant
        unset($data['email'], $data['password'], $data['is_active']);
        return $data;
    }
    
    protected function afterSave(): void
    {
        // Handle user account update/creation/deletion
        $email = $this->data['email'] ?? null;
        
        $user = User::where('profile_type', Enseignant::class)
            ->where('profile_id', $this->record->id_enseignant)
            ->first();
        
        if (filled($email)) {
            // User wants an account
            $userData = [
                'name' => trim(($this->record->prenom ?? '') . ' ' . ($this->record->nom ?? '')),
                'email' => $email,
                'is_active' => $this->data['is_active'] ?? true,
            ];
            
            // Only update password if provided
            if (filled($this->data['password'])) {
                $userData['password'] = bcrypt($this->data['password']);
            }
            
            if ($user) {
                // Update existing user
                $user->update($userData);
            } else {
                // Create new user account
                if (empty($this->data['password'])) {
                    $userData['password'] = bcrypt('teacher123');
                }
                
                $userData['profile_type'] = Enseignant::class;
                $userData['profile_id'] = $this->record->id_enseignant;
                
                $user = User::create($userData);
                
                // Assign teacher role
                $user->assignRole('teacher');
            }
        } else {
            // No email = delete user account if exists
            if ($user) {
                $user->delete();
            }
        }
    }
}
