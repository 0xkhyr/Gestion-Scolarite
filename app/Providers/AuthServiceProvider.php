<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Etudiant;
use App\Policies\EtudiantPolicy;
use App\Models\Enseignant;
use App\Policies\EnseignantPolicy;
use App\Models\Evaluation;
use App\Policies\EvaluationPolicy;
use App\Models\Note;
use App\Policies\NotePolicy;
use App\Models\Classe;
use App\Policies\ClassePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Etudiant::class => EtudiantPolicy::class,
        Enseignant::class => EnseignantPolicy::class,
        Evaluation::class => EvaluationPolicy::class,
        Note::class => NotePolicy::class,
        Classe::class => ClassePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
