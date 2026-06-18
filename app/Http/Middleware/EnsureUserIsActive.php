<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cleanly sign out a deactivated account and send it to the login page with a
 * message — instead of the raw 403 Filament's Authenticate middleware throws
 * when canAccessPanel() returns false for an already-authenticated session.
 * Must run before Filament\Http\Middleware\Authenticate.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Notification::make()
                ->title(__('app.account_deactivated'))
                ->danger()
                ->persistent()
                ->send();

            $loginUrl = Filament::getCurrentOrDefaultPanel()?->getLoginUrl()
                ?? route('filament.admin.auth.login');

            return redirect()->to($loginUrl);
        }

        return $next($request);
    }
}
