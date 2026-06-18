<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityLogger;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', __('app.login_required'));
        }

        $user = auth()->user();
        
        // Check if user account is active (if the attribute exists)
        if (isset($user->is_active) && !$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', __('app.account_deactivated'));
        }
        
        // Check if user has the required role
        if (!$this->hasRole($user, $role)) {
            $userRoles = $user->getRoleNames()->implode(', ') ?: 'none';
            
            Log::warning('Access denied: User attempted to access restricted route', [
                'user_id' => $user->id,
                'user_role' => $userRoles,
                'required_role' => $role,
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
            ]);

            ActivityLogger::log(
                get_class($user),
                $user->id,
                'access_denied',
                $request->path(),
                null,
                "Unauthorized access attempt. Required role: {$role}. User role: {$userRoles}",
                null,
                $request
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('app.unauthorized_access')
                ], 403);
            }
            if ($user->hasAnyRole(['admin', 'super_admin'])) {
                return redirect()->route('admin.dashboard')->with('error', __('app.acces_refuse'));
            }
            if ($user->hasAnyRole(['teacher', 'enseignant'])) {
                return redirect()->route('enseignant.dashboard')->with('error', __('app.acces_refuse'));
            }

            return redirect()->route('accueil')->with('error', __('app.acces_refuse'));
        }

        return $next($request);
    }

    /**
     * Check if user has the specified role
     */
    private function hasRole($user, string $role): bool
    {
        // Multiple roles separated by |
        if (str_contains($role, '|')) {
            $roles = explode('|', $role);
            foreach ($roles as $singleRole) {
                if ($user->hasRole(trim($singleRole))) {
                    return true;
                }
            }
            return false;
        }
        
        // Single role check
        return $user->hasRole($role);
    }
}
