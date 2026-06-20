<?php

namespace App\Filament\Resources\EtudiantResource\Pages;

use Filament\Actions\DeleteAction;
use App\Models\Etudiant;
use App\Filament\Resources\EtudiantResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\Rule;

class EditEtudiant extends EditRecord
{
    protected static string $resource = EtudiantResource::class;

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
        // Load user data if account exists
        if ($this->record->user) {
            $data['email'] = $this->record->user->email;
            $data['is_active'] = $this->record->user->is_active;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove user-related fields from student data
        unset($data['email'], $data['password'], $data['is_active']);

        return $data;
    }

    protected function afterSave(): void
    {
        $email = $this->data['email'] ?? null;
        
        if (filled($email)) {
            // Update or create user account
            $user = $this->record->user;
            
            $userData = [
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
                $userData['name'] = trim(($this->record->prenom ?? '') . ' ' . ($this->record->nom ?? ''));
                
                if (empty($this->data['password'])) {
                    $userData['password'] = bcrypt($this->record->matricule);
                }
                
                $userData['profile_type'] = Etudiant::class;
                $userData['profile_id'] = $this->record->id_etudiant;
                
                $user = User::create($userData);
                
                // Assign student role
                $user->assignRole('student');
            }
        } elseif ($this->record->user) {
            // Email removed - delete user account
            $this->record->user->delete();
        }
    }
}
