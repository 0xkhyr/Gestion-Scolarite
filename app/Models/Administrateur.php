<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Administrateur extends Model
{
    use HasFactory, Notifiable, LogsActivity;

    protected $primaryKey = 'id_administrateur';

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'adresse',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nom', 'prenom', 'telephone', 'adresse'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $hidden = [
        // Auth data is now in User model
    ];

    protected $casts = [
        // Move to User if needed
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    /**
     * Helper to check if admin has a user account
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
}
