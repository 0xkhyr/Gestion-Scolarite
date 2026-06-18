<?php

namespace App\Providers;

use App\Models\Classe;
use App\Models\Etudiant;
use App\Models\Enseignant;
use App\Models\Cours;
use App\Models\Evaluation;
use App\Models\Note;
use App\Models\EtudePaiement;
use App\Models\EnseignPaiement;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Route Model Bindings
        $this->configureModelBindings();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure route model bindings.
     */
    protected function configureModelBindings(): void
    {
        Route::model('classe', Classe::class);
        Route::model('etudiant', Etudiant::class);
        Route::model('enseignant', Enseignant::class);
        Route::model('cours', Cours::class);
        Route::model('evaluation', Evaluation::class);
        Route::model('note', Note::class);
        Route::model('etudePaiement', EtudePaiement::class);
        Route::model('enseignPaiement', EnseignPaiement::class);

        // Custom bindings for primary keys that are not 'id'
        Route::bind('classe', function ($value) {
            return Classe::where('id_classe', $value)->firstOrFail();
        });

        Route::bind('etudiant', function ($value) {
            return Etudiant::where('matricule', $value)->firstOrFail();
        });

        Route::bind('enseignant', function ($value) {
            return Enseignant::where('id_enseignant', $value)->firstOrFail();
        });

        Route::bind('cours', function ($value) {
            return Cours::where('id_cours', $value)->firstOrFail();
        });

        Route::bind('evaluation', function ($value) {
            return Evaluation::where('id_evaluation', $value)->firstOrFail();
        });

        Route::bind('note', function ($value) {
            return Note::where('id_note', $value)->firstOrFail();
        });
    }
}
