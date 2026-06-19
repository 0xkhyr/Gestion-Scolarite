<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Clean, organized route structure for School Management System
| Grouped by functionality with proper naming conventions
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Language Switching Routes
|--------------------------------------------------------------------------
*/
Route::get('/langue/{locale}', function ($locale) {
    $available = array_keys(config('locales', ['fr' => [], 'ar' => [], 'en' => []]));
    if (in_array($locale, $available, true)) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Public Website Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\PublicController;

// Dynamic theme CSS
Route::get('/css/theme.css', [PublicController::class, 'themeCSS'])->name('theme.css');

// Site information API endpoint
Route::get('/api/site-info', [PublicController::class, 'siteInfo'])->name('site.info');

// Search functionality
Route::get('/search', [PublicController::class, 'search'])->name('search');

// Contact form submission
Route::post('/contact', [PublicController::class, 'handleContactForm'])->name('contact.submit');

// Homepage - special handling
Route::get('/', [PublicController::class, 'homepage'])->name('homepage');

// Dynamic page routes - must be last to avoid conflicts
Route::get('/{slug}', [PublicController::class, 'showPage'])
    ->where('slug', '^(?!admin|teacher|api|health|langue).*$') // Exclude admin routes
    ->name('page.show');

/*
|--------------------------------------------------------------------------
| Legacy Routes (kept for backward compatibility)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Legacy Routes (kept for backward compatibility)
|--------------------------------------------------------------------------
*/
// Legacy welcome route - now redirects to homepage or dashboard
Route::get('/welcome', function () {
    // If user is authenticated, redirect to appropriate panel
    if (auth()->check()) {
        $redirectPath = \App\Services\RoleRedirectService::getRedirectPath(auth()->user());
        return redirect($redirectPath);
    }
    
    return redirect()->route('homepage');
})->name('accueil');

// Health check endpoint - restricted in production
Route::get('/health', function () {
    // Simple health check for production - just returns OK
    if (config('app.env') === 'production') {
        return response()->json([
            'status' => 'OK',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    // Detailed health check for development/staging
    try {
        $dbStatus = DB::connection()->getPdo() ? 'connected' : 'disconnected';
    } catch (\Exception $e) {
        $dbStatus = 'error: ' . $e->getMessage();
    }

    return response()->json([
        'status' => 'OK',
        'timestamp' => now()->toIso8601String(),
        'app' => config('app.name'),
        'env' => config('app.env'),
        'database' => $dbStatus,
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
    ]);
})->middleware('throttle:60,1'); // Rate limit: 60 requests per minute

