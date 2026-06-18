<?php

namespace App\Http\Middleware;

use App\Services\RoleRedirectService;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Use the RoleRedirectService for consistent login URL determination
        return RoleRedirectService::getLoginUrl($request);
    }
}
