<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Cours extends Model
{
    protected $primaryKey = 'id_cours';
    protected $fillable = ['jour','date','date_debut','date_fin','id_matiere','id_classe','id_enseignant','description'];

    // Cast time fields properly
    protected $casts = [
        'date' => 'date',
        'date_debut' => 'datetime:H:i',
        'date_fin' => 'datetime:H:i',
    ];

    /** Carbon dayOfWeek (Sun=0 … Sat=6) → French weekday stored in `jour`. */
    public const JOURS = [
        0 => 'dimanche', 1 => 'lundi', 2 => 'mardi', 3 => 'mercredi',
        4 => 'jeudi', 5 => 'vendredi', 6 => 'samedi',
    ];

    protected static function booted(): void
    {
        // A one-off session (date set) keeps `jour` in sync with its real
        // weekday, so day-based filters and the timetable never go stale.
        static::saving(function (Cours $cours): void {
            if ($cours->date) {
                $cours->jour = self::JOURS[Carbon::parse($cours->date)->dayOfWeek];
            }
        });
    }

    /** A recurring weekly slot (no specific date) vs a one-off session. */
    public function isRecurring(): bool
    {
        return $this->date === null;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_cours';
    }
    
    public function classe()
    {
        return $this->belongsTo(Classe::class,'id_classe');
    }
    
    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class,'id_enseignant');
    }
    
    public function matiere()
    {
        return $this->belongsTo(Matiere::class,'id_matiere','id_matiere');
    }
    
    use HasFactory;
}
