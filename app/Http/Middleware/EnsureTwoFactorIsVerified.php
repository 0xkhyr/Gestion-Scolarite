<?php

namespace App\Http\Middleware;

use App\Services\TwoFactorService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorIsVerified
{
    public function __construct(
        protected TwoFactorService $twoFactorService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Skip for 2FA pages and logout
        if ($this->isExemptRoute($request)) {
            return $next($request);
        }

        // If 2FA is required — either globally (config) or for this specific
        // account (two_factor_required flag set by an admin) — and the user
        // hasn't confirmed it yet, send them to their account Security page to
        // enrol (no dedicated setup route exists; enrolment lives there).
        if (config('security.require_2fa', false) || $user->two_factor_required) {
            if (!$this->twoFactorService->isConfirmed($user)) {

                return redirect()->route('filament.admin.account.pages.security');
            }
        }

        // If 2FA is enabled and confirmed, check if needs reconfirmation
        if ($this->twoFactorService->isConfirmed($user)) {
            if (!$this->twoFactorService->isRecentlyVerified($user)) {
                session(['2fa_intended_url' => $request->url()]);

                return redirect()->route('filament.admin.pages.two-factor-challenge');
            }
        }

        return $next($request);
    }

    /**
     * Check if the route is exempt from 2FA verification.
     */
    protected function isExemptRoute(Request $request): bool
    {
        $exemptRoutes = [
            // Enrolment page (where users turn 2FA on) must stay reachable.
            'filament.admin.account.pages.security',
            'filament.admin.pages.two-factor-challenge',
            'filament.admin.auth.logout',
            'logout',
        ];

        // If the named route exists and is in the exempt list, skip
        if (in_array($request->route()?->getName(), $exemptRoutes)) {
            return true;
        }

        // Also allow direct path matches (avoids reliance on named routes).
        if (str_starts_with($request->path(), 'admin/account/security')
            || str_starts_with($request->path(), 'admin/two-factor-challenge')) {
            return true;
        }

        return false;
    }
}
