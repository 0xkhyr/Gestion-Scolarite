<?php

namespace App\Http\Middleware;

use App\Services\RoleRedirectService;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Use the RoleRedirectService for consistent redirect logic
                $redirectPath = RoleRedirectService::getRedirectPath($user);
                return redirect($redirectPath);
            }
        }

        return $next($request);
    }
}
