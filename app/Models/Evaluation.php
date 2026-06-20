<?php

namespace App\Models;

use App\Events\EvaluationCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $table = 'evaluations';
    protected $primaryKey = 'id_evaluation';
    protected $fillable = ['id_matiere','titre','date','type','date_debut','date_fin','id_classe','note_max'];
    
    protected $casts = [
        'date' => 'date',
        'note_max' => 'decimal:2',
    ];
    
    public function classe()
    {
        return $this->belongsTo(Classe::class,'id_classe');
    }
    
    public function notes()
    {
        return $this->hasMany(Note::class, 'id_evaluation');
    }
    
    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'id_matiere');
    }
    
    // Helper method to get matiere name
    public function getMatiereNameAttribute()
    {
        return $this->matiere ? $this->matiere->nom_matiere : 'N/A';
    }
    
    // Helper method to get full evaluation title
    public function getFullTitleAttribute()
    {
        return $this->titre ?: (ucfirst($this->type) . ' de ' . $this->matiere_name);
    }

    /**
     * Check if evaluation has ended (based on date)
     */
    public function hasEnded(): bool
    {
        if (!$this->date) {
            return false;
        }
        return $this->date->isPast();
    }

    /**
     * Check if evaluation has any grades entered
     */
    public function hasGrades(): bool
    {
        return $this->notes()->exists();
    }

    /**
     * Check if evaluation can be modified
     * An evaluation is locked if it has ended OR has grades
     */
    public function isLocked(): bool
    {
        return $this->hasEnded() || $this->hasGrades();
    }

    protected static function booted()
    {
        static::created(function ($evaluation) {
            event(new EvaluationCreated($evaluation));
        });
    }
    
    use HasFactory;
}
