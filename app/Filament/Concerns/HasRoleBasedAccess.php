<?php

namespace App\Filament\Concerns;

use App\Models\Etudiant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasRoleBasedAccess
{
    /**
     * Apply role-based query scoping for teachers
     */
    protected static function applyTeacherScope(Builder $query): Builder
    {
        $user = auth()->user();
        
        // Super admin bypass
        if ($user->hasRole('super_admin')) {
            return $query;
        }
        
        if (!$user->hasRole(['teacher', 'enseignant'])) {
            return $query;
        }
        
        $enseignant = $user->profile;
        if (!$enseignant) {
            return $query->whereRaw('1 = 0');
        }
        
        $teacherClasses = $enseignant->classes()->pluck('classes.id_classe');
        
        return $query->whereHas('classe', function (Builder $q) use ($teacherClasses) {
            $q->whereIn('id_classe', $teacherClasses);
        });
    }
    
    /**
     * Apply role-based query scoping for students
     */
    protected static function applyStudentScope(Builder $query, string $studentIdColumn = 'id_etudiant'): Builder
    {
        $user = auth()->user();
        
        // Super admin bypass
        if ($user->hasRole('super_admin')) {
            return $query;
        }
        
        if (!$user->hasRole('etudiant')) {
            return $query;
        }
        
        $etudiant = $user->profile;
        if (!$etudiant) {
            return $query->whereRaw('1 = 0');
        }
        
        return $query->where($studentIdColumn, $etudiant->id_etudiant);
    }
    
    /**
     * Apply role-based query scoping for table data
     */
    protected static function applyRoleBasedTableScope(Builder $query, array $options = []): Builder
    {
        $user = auth()->user();
        
        // Admins see everything
        if ($user->hasRole('super_admin')) {
            return $query;
        }
        
        // Apply teacher scope if needed
        if (($user->hasRole('enseignant') || $user->hasRole('teacher')) && ($options['teacherScope'] ?? true)) {
            if (isset($options['teacherScopeMethod'])) {
                return static::{$options['teacherScopeMethod']}($query);
            }
            
            // Default teacher scope - assumes model has a classe relationship
            $enseignant = $user->profile;
            if ($enseignant) {
                $teacherClasses = $enseignant->classes()->pluck('classes.id_classe');
                
                if (isset($options['classColumn'])) {
                    // Direct class column on the model
                    return $query->whereIn($options['classColumn'], $teacherClasses);
                } else {
                    // Relationship-based scoping
                    $relationName = $options['classRelation'] ?? 'classe';
                    return $query->whereHas($relationName, function (Builder $q) use ($teacherClasses) {
                        $q->whereIn('id_classe', $teacherClasses);
                    });
                }
            } else {
                return $query->whereRaw('1 = 0');
            }
        }
        
        // Apply student scope if needed
        if ($user->hasRole('etudiant') && ($options['studentScope'] ?? true)) {
            if (isset($options['studentScopeMethod'])) {
                return static::{$options['studentScopeMethod']}($query);
            }
            
            return static::applyStudentScope($query, $options['studentIdColumn'] ?? 'id_etudiant');
        }
        
        return $query;
    }
    
    /**
     * Check if a teacher can access a specific record
     */
    protected static function canTeacherAccessRecord(Model $record): bool
    {
        $user = auth()->user();
        
        // Super admin bypass
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        if (!$user->hasRole(['teacher', 'enseignant'])) {
            return false;
        }
        
        $enseignant = $user->profile;
        if (!$enseignant) {
            return false;
        }
        
        $teacherClasses = $enseignant->classes()->pluck('classes.id_classe');
        
        // Check if record has direct classe relationship
        if (method_exists($record, 'classe') && $record->classe) {
            return $teacherClasses->contains($record->classe->id_classe);
        }
        
        // Check if record has id_classe column
        if (isset($record->id_classe)) {
            return $teacherClasses->contains($record->id_classe);
        }
        
        // Check through student relationship (for notes, evaluations, etc.)
        if (method_exists($record, 'etudiant') && $record->etudiant) {
            return $teacherClasses->contains($record->etudiant->id_classe);
        }
        
        return false;
    }
    
    /**
     * Check if a student can access their own record
     */
    protected static function canStudentAccessRecord(Model $record): bool
    {
        $user = auth()->user();
        
        // Super admin bypass
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        if (!$user->hasRole('etudiant')) {
            return false;
        }
        
        $etudiant = $user->profile;
        if (!$etudiant) {
            return false;
        }
        
        // Direct student record
        if (isset($record->id_etudiant)) {
            return $record->id_etudiant === $etudiant->id_etudiant;
        }
        
        // Student's own profile
        if ($record instanceof Etudiant) {
            return $record->id_etudiant === $etudiant->id_etudiant;
        }
        
        return false;
    }
    
    /**
     * Apply role-based restrictions to relationship queries
     */
    protected static function applyRoleBasedRelationScope(Builder $query, array $options = []): Builder
    {
        $user = auth()->user();
        
        // Admins see all options
        if ($user->hasRole('super_admin')) {
            return $query;
        }
        
        // Teachers see limited options based on their classes
        if ($user->hasRole(['teacher', 'enseignant'])) {
            $enseignant = $user->profile;
            if ($enseignant) {
                $teacherClasses = $enseignant->classes()->pluck('classes.id_classe');
                
                if (isset($options['classColumn'])) {
                    return $query->whereIn($options['classColumn'], $teacherClasses);
                }
            } else {
                return $query->whereRaw('1 = 0');
            }
        }
        
        // Students see very limited options (usually their own class only)
        if ($user->hasRole('etudiant')) {
            $etudiant = $user->profile;
            if ($etudiant && isset($options['classColumn'])) {
                return $query->where($options['classColumn'], $etudiant->id_classe);
            } else {
                return $query->whereRaw('1 = 0');
            }
        }
        
        return $query;
    }
}