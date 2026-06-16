<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    use HasFactory;
    
    protected $table = 'matieres';
    protected $primaryKey = 'id_matiere';
    
    protected $fillable = [
        'nom_matiere',
        'code_matiere',
        'description',
        'coefficient',
        'note_max',
        'serie_coefficients',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'coefficient' => 'integer',
        'note_max' => 'decimal:2',
        'serie_coefficients' => 'array',
    ];

    /**
     * Effective coefficient for this subject in a given série.
     * Lycée subjects can override per-série via `serie_coefficients`
     * ({"C":9,"D":7,...}); otherwise the global `coefficient` is used.
     */
    public function coefficientForSerie(?string $serie): float
    {
        if ($serie && is_array($this->serie_coefficients)
            && isset($this->serie_coefficients[$serie])
            && $this->serie_coefficients[$serie] !== null
            && $this->serie_coefficients[$serie] !== '') {
            return (float) $this->serie_coefficients[$serie];
        }

        return (float) ($this->coefficient ?: 1);
    }
    
    // Relationships
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'id_matiere');
    }
    
    public function enseignants()
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_matiere_classe', 'id_matiere', 'id_enseignant')
                    ->withPivot('id_classe', 'active')
                    ->withTimestamps();
    }
    
    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'enseignant_matiere_classe', 'id_matiere', 'id_classe')
                    ->withPivot('id_enseignant', 'active')
                    ->withTimestamps();
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    // Helper methods
    public function getFullNameAttribute()
    {
        return $this->code_matiere . ' - ' . $this->nom_matiere;
    }
}
