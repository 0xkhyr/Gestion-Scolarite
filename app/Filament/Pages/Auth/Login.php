<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Login extends \Filament\Auth\Pages\Login
{
    public function mount(): void
    {
        parent::mount();

        // Determine which panel role to use for demo credentials (admin, staff, teacher)
        // Priority: env('DEMO_PANEL') -> infer from request path -> default to 'admin'
        $role = $this->determinePanelRole();

        // Load credentials from env: DEMO_<ROLE>_EMAIL / DEMO_<ROLE>_PASSWORD
        // Fallbacks fall back to admin demo credentials for safety
        $emailKey = 'DEMO_' . strtoupper($role) . '_EMAIL';
        $passwordKey = 'DEMO_' . strtoupper($role) . '_PASSWORD';

        // Role-specific default credentials (match seeded demo users)
        $defaults = [
            'admin' => ['email' => 'admin@ecole.com', 'password' => 'password123'],
            'staff' => ['email' => 'secretaire@ecole.com', 'password' => 'password123'],
            'teacher' => ['email' => 'aminetou@ecole.com', 'password' => 'teacher123'],
        ];

        $email = env($emailKey, $defaults[$role]['email']);
        $password = env($passwordKey, $defaults[$role]['password']);

        $this->form->fill([
            'email' => $email,
            'password' => $password,
        ]);
    }

    /**
     * Determine which demo panel role to use.
     */
    protected function determinePanelRole(): string
    {
        // Explicit override via env
        $envRole = Str::lower(env('DEMO_PANEL', ''));
        if (in_array($envRole, ['admin', 'staff', 'teacher'], true)) {
            return $envRole;
        }

        // Infer from request path (very simple heuristic)
        $path = request()->path();
        if (Str::contains($path, ['staff', 'secretaire'])) {
            return 'staff';
        }
        if (Str::contains($path, ['teacher', 'teasher', 'enseignant'])) {
            return 'teacher';
        }

        // Default to admin
        return 'admin';
    }
    public function authenticate(): ?LoginResponse
    {
        // Get security settings from database
        $maxAttempts = (int) setting('security.max_login_attempts', 5);
        $lockoutDuration = (int) setting('security.lockout_duration', 15); // minutes
        
        $email = strtolower($this->data['email'] ?? '');
        
        // Find user by email
        $user = User::where('email', $email)->first();
        
        // CRITICAL: Check database lockout FIRST
        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            $remainingMinutes = now()->diffInMinutes($user->locked_until, false);
            $remainingMinutes = ceil(abs($remainingMinutes));
            
            // Audit: Login attempt on locked account - BLOCKED
            activity('security')
                ->causedBy(null)
                ->performedOn($user)
                ->withProperties([
                    'email' => $email,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'locked_until' => $user->locked_until->toDateTimeString(),
                    'remaining_minutes' => $remainingMinutes,
                    'event_type' => 'login_blocked_locked',
                ])
                ->log("LOGIN BLOCKED: Account locked until {$user->locked_until->format('Y-m-d H:i:s')} ({$remainingMinutes} minutes remaining)");
            
            // Show clear error to user
            Notification::make()
                ->danger()
                ->title(__('app.account_locked'))
                ->body(__('app.account_locked_temporarily', ['minutes' => $remainingMinutes]))
                ->persistent()
                ->send();
            
            // Fire lockout event
            $request = request();
            $request->merge(['email' => $email]);
            event(new Lockout($request));
            
            // STOP - Do not proceed with authentication
            return null;
        }

        // Attempt authentication (account is not locked)
        try {
            $response = parent::authenticate();
            
            // SUCCESS: Clear failed attempts and log successful login
            if ($response) {
                $authenticatedUser = auth()->user();
                
                // Clear lockout fields on successful login
                $authenticatedUser->update([
                    'failed_login_attempts' => 0,
                    'locked_until' => null,
                    'last_failed_login_at' => null,
                ]);
                
                activity('security')
                    ->causedBy($authenticatedUser)
                    ->performedOn($authenticatedUser)
                    ->withProperties([
                        'email' => $email,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'event_type' => 'login_success',
                    ])
                    ->log('Successful login');
            }
            
            return $response;
            
        } catch (ValidationException $e) {
            // FAILED: Track failed attempt in database
            if ($user) {
                $currentAttempts = $user->failed_login_attempts ?? 0;
                $newAttempts = $currentAttempts + 1;
                $remainingAttempts = max(0, $maxAttempts - $newAttempts);
                
                // Update user with failed attempt
                $user->update([
                    'failed_login_attempts' => $newAttempts,
                    'last_failed_login_at' => now(),
                ]);
                
                // Log the failed attempt
                activity('security')
                    ->causedBy(null)
                    ->performedOn($user)
                    ->withProperties([
                        'email' => $email,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'attempt_number' => $newAttempts,
                        'max_attempts' => $maxAttempts,
                        'remaining_attempts' => $remainingAttempts,
                        'event_type' => 'login_failed',
                    ])
                    ->log("Failed login attempt (Attempt {$newAttempts} of {$maxAttempts})");
                
                // If max attempts reached, LOCK THE ACCOUNT in database
                if ($newAttempts >= $maxAttempts) {
                    $lockedUntil = now()->addMinutes($lockoutDuration);
                    
                    $user->update([
                        'locked_until' => $lockedUntil,
                    ]);
                    
                    activity('security')
                        ->causedBy(null)
                        ->performedOn($user)
                        ->withProperties([
                            'email' => $email,
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'max_attempts' => $maxAttempts,
                            'lockout_duration_minutes' => $lockoutDuration,
                            'locked_until' => $lockedUntil->toDateTimeString(),
                            'event_type' => 'brute_force_attack',
                        ])
                        ->log("BRUTE FORCE ATTACK: Account locked until {$lockedUntil->format('Y-m-d H:i:s')} after {$maxAttempts} failed attempts");
                    
                    // Fire lockout event
                    $request = request();
                    $request->merge(['email' => $email]);
                    event(new Lockout($request));
                    
                    // Update error message to show lockout
                    Notification::make()
                        ->danger()
                        ->title(__('app.account_locked'))
                        ->body(__('app.account_locked_too_many', ['minutes' => $lockoutDuration]))
                        ->persistent()
                        ->send();
                }
            } else {
                // User not found - still log the attempt (don't reveal if email exists)
                activity('security')
                    ->causedBy(null)
                    ->withProperties([
                        'email' => $email,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'event_type' => 'login_failed_unknown_user',
                    ])
                    ->log("Failed login attempt for non-existent user");
            }
            
            // Re-throw the validation exception to show error to user
            throw $e;
        }
    }
}
