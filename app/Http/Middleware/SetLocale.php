<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, fallback to config default
        $locale = session('locale', config('app.locale'));
        
        // Validate locale against configured locales
        $available = array_keys(config('locales', ['fr' => [], 'ar' => [], 'en' => []]));
        if (in_array($locale, $available, true)) {
            app()->setLocale($locale);
            Carbon::setLocale($locale);
        }
        
        return $next($request);
    }
}
