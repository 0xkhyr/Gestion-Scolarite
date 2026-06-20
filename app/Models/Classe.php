<?php

namespace App\Models;

use App\Support\Academic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class Classe extends Model
{
    protected $primaryKey = 'id_classe';
    protected $fillable = ['nom_classe', 'niveau', 'serie', 'groupe', 'nom_classe_translations', 'niveau_translations'];

    protected $casts = [
        'groupe' => 'integer',
        'nom_classe_translations' => 'array',
        'niveau_translations' => 'array',
    ];

    protected static function booted(): void
    {
        // Keep the stored nom_classe as a stable French identifier derived from
        // the structured fields (the translated display name is the `label`
        // accessor). This keeps URL keys / lookups / uniqueness locale-neutral.
        static::saving(function (Classe $classe): void {
            if (blank($classe->niveau)) {
                return;
            }

            $name = trans('app.' . $classe->niveau, [], 'fr');
            if ($name === 'app.' . $classe->niveau) {
                $name = (string) $classe->niveau;
            }
            if (filled($classe->serie)) {
                $name .= ' ' . $classe->serie;
            }

            $classe->nom_classe = $name . ' — G' . ($classe->groupe ?: 1);
        });
    }

    // Cycle this class belongs to (fondamental|college|lycee), via config/academic.
    public function getCycleAttribute(): ?string
    {
        return Academic::cycleForLevel($this->niveau);
    }

    /** Per-request cache of group counts keyed by "niveau|serie". */
    protected static ?array $groupCounts = null;

    /** Does this level+série have more than one group? (drives the -G suffix) */
    public function hasMultipleGroups(): bool
    {
        if (self::$groupCounts === null) {
            self::$groupCounts = static::query()
                ->selectRaw("niveau, COALESCE(serie, '') as serie_key, COUNT(*) as total")
                ->groupBy('niveau', 'serie')
                ->get()
                ->mapWithKeys(fn ($r) => [$r->niveau . '|' . $r->serie_key => (int) $r->total])
                ->all();
        }

        return (self::$groupCounts[$this->niveau . '|' . ($this->serie ?? '')] ?? 1) > 1;
    }

    /**
     * Canonical short code for the class group: level + série (lycée) [+ group].
     * Locale-neutral, stable — used as an identifier. The "-G{n}" suffix only
     * appears when the level is split into several groups. e.g. "1AS", "5AS C",
     * "1AS-G1" / "1AS-G2".
     */
    public function getCodeAttribute(): string
    {
        $code = (string) $this->niveau;

        if (filled($this->serie)) {
            $code .= ' ' . $this->serie;
        }

        if ($this->hasMultipleGroups()) {
            $code .= '-G' . ($this->groupe ?: 1);
        }

        return $code;
    }

    /**
     * Human, translated display name — follows the UI language because the level
     * label resolves through __('app.<niveau>'). The "— G{n}" suffix only appears
     * when the level is split. e.g. "1re année secondaire C", "1re année secondaire — G2".
     * Falls back to the stored nom_classe for any legacy/uncoded row.
     */
    public function getLabelAttribute(): string
    {
        if (blank($this->niveau)) {
            return (string) $this->nom_classe;
        }

        $label = Academic::levelLabel($this->niveau);

        if (filled($this->serie)) {
            $label .= ' ' . $this->serie;
        }

        if ($this->hasMultipleGroups()) {
            $label .= ' — G' . ($this->groupe ?: 1);
        }

        return $label;
    }
    
    public function etudiants()
    {
        return $this->hasMany(Etudiant::class, 'id_classe');
    }
    
    public function enseignants()
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_matiere_classe', 'id_classe', 'id_enseignant')
                    ->withPivot('id_matiere', 'active')
                    ->withTimestamps();
    }
    
    public function cours()
    {
        return $this->hasMany(Cours::class, 'id_classe');
    }
    
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'id_classe');
    }
    
    public function notes()
    {
        return $this->hasMany(Note::class, 'id_classe');
    }
    
    // Helper methods for translations
    public function getTranslatedName($locale = 'fr')
    {
        if ($this->nom_classe_translations && isset($this->nom_classe_translations[$locale])) {
            return $this->nom_classe_translations[$locale];
        }
        return $this->nom_classe;
    }
    
    public function getTranslatedLevel($locale = 'fr')
    {
        if ($this->niveau_translations && isset($this->niveau_translations[$locale])) {
            return $this->niveau_translations[$locale];
        }
        return $this->niveau;
    }
    
    use HasFactory, Translatable;
}
