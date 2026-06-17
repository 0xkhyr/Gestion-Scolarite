<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * Gate panel access. Filament enforces this both at login and on every
     * request, for all panels — so a deactivated account cannot sign in or
     * keep an existing session. Role-based separation between panels stays in
     * the per-panel middleware (EnsureAdminRole / TeacherMiddleware).
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'is_active',
        'profile_type',
        'profile_id',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_enabled',
        'two_factor_required',
        'last_login_at',
        'locked_until',
        'failed_login_attempts',
        'last_failed_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'two_factor_required' => 'boolean',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'last_failed_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
    ];

    /**
     * Model-level invariants kept in one place on save:
     *  - stamp password_changed_at whenever the password changes (covers create,
     *    profile change, Fortify update, and password reset);
     *  - keep the two_factor_enabled column as a reliable mirror of the source of
     *    truth (two_factor_confirmed_at), so it can never drift no matter which
     *    code path toggles 2FA (Fortify, enrolment flow, admin actions, imports).
     */
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->isDirty('password')) {
                $user->password_changed_at = now();
            }

            $user->two_factor_enabled = filled($user->two_factor_confirmed_at);
        });
    }

    /**
     * Has this user's password expired under the optional expiry policy?
     * Returns false unless the policy is enabled and a positive day count is set.
     */
    public function passwordExpired(): bool
    {
        if (! setting('security.password_expiry_enabled', false)) {
            return false;
        }

        $days = (int) setting('security.password_expiry_days', 0);
        if ($days <= 0) {
            return false;
        }

        // Fall back to created_at for users who predate password tracking.
        $changedAt = $this->password_changed_at ?? $this->created_at;
        if (! $changedAt) {
            return false;
        }

        return $changedAt->copy()->addDays($days)->isPast();
    }

    /**
     * Get the profile associated with the user.
     */
    public function profile()
    {
        return $this->morphTo();
    }

    /**
     * Check if user is an administrator
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['admin', 'super_admin']);
    }

    /**
     * Check if user is a teacher
     */
    public function isTeacher(): bool
    {
        return $this->hasAnyRole(['teacher', 'enseignant']);
    }

    /**
     * Check if user is a teacher (alias)
     */
    public function isEnseignant(): bool
    {
        return $this->isTeacher();
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->hasAnyRole(['student', 'etudiant']);
    }



    /**
     * Get user's name from profile or database
     */
    public function getNameAttribute($value): string
    {
        // If name is set in database, use it
        if ($value) {
            return $value;
        }
        
        // Otherwise, get from profile
        if ($this->profile) {
            return trim(($this->profile->prenom ?? '') . ' ' . ($this->profile->nom ?? ''));
        }
        
        return '';
    }

    /**
     * Get user's full name (alias for name)
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get the classe this user belongs to (only for students via Etudiant model)
     * Teachers' classes are managed through enseignant_matiere_classe pivot table
     */
    public function classe()
    {
        if ($this->isEtudiant()) {
            // For students, get class through etudiant record
            $etudiant = Etudiant::where('email', $this->email)->first();
            return $etudiant ? $etudiant->classe() : null;
        }
        return null;
    }

    /**
     * Get students for this teacher (if user is a teacher)
     */
    public function etudiants()
    {
        if ($this->isEnseignant()) {
            // Get students through the classes this teacher teaches
            $teacherClasses = $this->classesEnseignees()->pluck('id_classe');
            return Etudiant::whereIn('id_classe', $teacherClasses);
        }
        return null;
    }

    /**
     * Get courses for this teacher (if user is a teacher)
     */
    public function cours()
    {
        if ($this->isEnseignant()) {
            return $this->hasMany(Cours::class, 'id_enseignant');
        }
        return null;
    }

    /**
     * Get notes for this student (if user is a student)
     */
    public function notes()
    {
        if ($this->isEtudiant()) {
            return $this->hasMany(Note::class, 'id_etudiant');
        }
        return null;
    }

    /**
     * Get payments for this teacher (if user is a teacher)
     */
    public function paiements()
    {
        if ($this->isEnseignant()) {
            return $this->hasMany(EnseignPaiement::class, 'id_enseignant');
        } elseif ($this->isEtudiant()) {
            return $this->hasMany(EtudePaiement::class, 'id_etudiant');
        }
        return null;
    }

    /**
     * Get subjects (matieres) this teacher teaches
     * Teachers should use the Enseignant model for subject relationships
     */
    public function matieres()
    {
        if ($this->isEnseignant()) {
            $enseignant = Enseignant::where('email', $this->email)->first();
            return $enseignant ? $enseignant->matieres() : collect();
        }
        return collect();
    }

    /**
     * Get classes this teacher teaches (through subjects)
     * Teachers should use the Enseignant model for class relationships
     */
    public function classesEnseignees()
    {
        if ($this->isEnseignant()) {
            $enseignant = Enseignant::where('email', $this->email)->first();
            return $enseignant ? $enseignant->classes() : collect();
        }
        return collect();
    }

    /**
     * Get subjects this teacher teaches in a specific class
     */
    public function matieresInClasse($classeId)
    {
        if ($this->isEnseignant()) {
            return $this->matieres()->wherePivot('id_classe', $classeId)->wherePivot('active', true);
        }
        return collect();
    }

    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get users by role
     */
    public function scopeRole($query, $role)
    {
        return $query->role($role);
    }

    /**
     * Get all admins
     */
    public static function getAdmins()
    {
        return self::role('admin')->active()->get();
    }

    /**
     * Get all teachers
     */
    public static function getEnseignants()
    {
        return self::where('role', 'enseignant')->active()->get();
    }

    /**
     * Get all students
     */
    public static function getEtudiants()
    {
        return self::where('role', 'etudiant')->active()->get();
    }

    /**
     * Configure activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the notification preferences for the user.
     */
    public function notificationPreferences()
    {
        return $this->hasMany(\App\Models\NotificationPreference::class);
    }

    /**
     * Get the notification logs for the user.
     */
    public function notificationLogs()
    {
        return $this->hasMany(\App\Models\NotificationLog::class);
    }
}
