<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Enseignant extends Model implements HasMedia
{
    use Notifiable, HasFactory, LogsActivity, InteractsWithMedia;

    // Single profile photo on the public disk. No image conversions (no GD/
    // Imagick on this host) — the client-cropped original is stored/displayed.
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->singleFile()
            ->useDisk('public');
    }

    protected $primaryKey = 'id_enseignant';
    
    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'adresse',
    ];

    protected $hidden = [];

    protected $casts = [];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    /**
     * Helper to check if teacher has a user account
     */
    public function hasAccount(): bool
    {
        return $this->user !== null;
    }
    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }
    
    // Removed direct classe relationship - now handled through pivot table
    
    public function cours()
    {
        return $this->hasMany(Cours::class, 'id_enseignant');
    }
    
    public function paiements()
    {
        return $this->hasMany(EnseignPaiement::class, 'id_enseignant');
    }
    
    // New matiere relationships
    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'enseignant_matiere_classe', 'id_enseignant', 'id_matiere')
                    ->withPivot('id_classe', 'active')
                    ->withTimestamps();
    }
    
    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'enseignant_matiere_classe', 'id_enseignant', 'id_classe')
                    ->withPivot('id_matiere', 'active')
                    ->withTimestamps();
    }

    /**
     * A teacher is always an 'enseignant' role in the system
     */
    public function hasRole(string $role): bool
    {
        return $role === 'enseignant';
    }

    /**
     * Configure activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nom', 'prenom', 'email', 'telephone', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Teacher {$eventName}");
    }
}
