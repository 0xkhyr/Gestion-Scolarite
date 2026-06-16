<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces the optional password-expiry policy. No-op unless
 * security.password_expiry_enabled is on (User::passwordExpired() handles that).
 * Expired users are sent to their account Security page to set a new password.
 */
class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Let the user reach the page where they actually change the password,
        // plus logout and the 2FA flow, without bouncing.
        if ($this->isExemptRoute($request)) {
            return $next($request);
        }

        if ($user->passwordExpired()) {
            Notification::make()
                ->title(__('app.password_expired_title'))
                ->body(__('app.password_expired_body'))
                ->warning()
                ->persistent()
                ->send();

            return redirect()->route('filament.admin.pages.account.security');
        }

        return $next($request);
    }

    protected function isExemptRoute(Request $request): bool
    {
        $exemptRoutes = [
            'filament.admin.pages.account.security',
            'filament.admin.pages.two-factor-challenge',
            'filament.admin.auth.logout',
            'logout',
        ];

        if (in_array($request->route()?->getName(), $exemptRoutes, true)) {
            return true;
        }

        return str_starts_with($request->path(), 'admin/account/security')
            || str_starts_with($request->path(), 'admin/two-factor-challenge');
    }
}
