<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Etudiant extends Model implements HasMedia
{
    use HasFactory, LogsActivity, InteractsWithMedia;

    // Single profile photo on the public disk. No image conversions: this host
    // has neither GD nor Imagick, so the (client-cropped) original is stored
    // and displayed as-is. Add a 'thumb' conversion later if a driver is added.
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->singleFile()
            ->useDisk('public');
    }
    
    protected $primaryKey = 'id_etudiant';
    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'telephone',
        'adresse',
        'date_naissance',
        'genre',
        'id_classe'
    ];
    
    protected $casts = [
        'date_naissance' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->matricule)) {
                $model->matricule = self::generateMatricule();
            }
        });
    }

    public static function generateMatricule(): string
    {
        // Get school configuration
        $config = config('school.matricule');
        $schoolCode = $config['school_code'];
        $yearFormat = $config['year_format'];
        $sequenceLength = $config['sequence_length'];
        
        // Generate year based on format
        $year = date($yearFormat);
        
        // Create the prefix pattern
        $prefix = $schoolCode . $year;
        $pattern = $prefix . '%';
        
        // Find the last matricule with this prefix to ensure continuity
        $lastStudent = self::where('matricule', 'LIKE', $pattern)
                           ->orderBy('matricule', 'desc')
                           ->first();
        
        // Calculate next sequence number
        if ($lastStudent) {
            // Extract the sequence number from the last matricule
            $lastSequence = (int) substr($lastStudent->matricule, strlen($prefix));
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }
        
        // Generate the complete matricule
        $matricule = $prefix . str_pad($nextSequence, $sequenceLength, '0', STR_PAD_LEFT);
        
        // Double-check uniqueness (safety net)
        while (self::where('matricule', $matricule)->exists()) {
            $nextSequence++;
            $matricule = $prefix . str_pad($nextSequence, $sequenceLength, '0', STR_PAD_LEFT);
        }
        
        return $matricule;
    }

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    /**
     * Check if this student has a user account
     */
    public function hasAccount(): bool
    {
        return $this->user()->exists();
    }

    /**
     * Get email from user account if exists
     */
    public function getEmailAttribute()
    {
        return $this->user?->email;
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->nom} {$this->prenom}");
    }
    
    public function classe()
    {
        return $this->belongsTo(Classe::class, 'id_classe');
    }
    
    public function notes()
    {
        return $this->hasMany(Note::class, 'id_etudiant');
    }
    
    public function paiements()
    {
        return $this->hasMany(EtudePaiement::class, 'id_etudiant');
    }
    


    // Route model binding key - use matricule in URLs
    public function getRouteKeyName()
    {
        return 'matricule';
    }

    /**
     * Configure activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nom', 'prenom', 'email', 'telephone', 'id_classe', 'matricule'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Student {$eventName}");
    }
    
    use HasFactory;
}
