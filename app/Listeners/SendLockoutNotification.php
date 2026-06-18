<?php

namespace App\Listeners;

use App\Models\NotificationLog;
use Illuminate\Auth\Events\Lockout;
use App\Services\NotificationService;
use App\Support\NotificationKeys;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SendLockoutNotification
{
    protected NotificationService $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(Lockout $event): void
    {
        // Debugging: Log the request data to see what we are getting
        Log::info('Lockout Event Fired (Sync)', [
            'inputs' => $event->request->all(),
            'email_input' => $event->request->input('email'),
            'data_email' => $event->request->input('data.email'),
        ]);

        // Try standard input first
        $email = $event->request->input('email');
        
        // If empty, try 'data.email' (Filament struct)
        if (empty($email)) {
            $email = $event->request->input('data.email');
        }

        Log::info("Lockout Listener: Processing email [{$email}]");

        // Find the user who is being locked out
        $user = User::where('email', $email)->first();

        if ($user) {
            // Prevent spam detected: Check if a similar notification was sent in the last 5 minutes
            $recentNotification = NotificationLog::where('user_id', $user->id)
                ->where('key', NotificationKeys::SECURITY_ALERT)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->exists();
            
            if ($recentNotification) {
                Log::info("Lockout Listener: Notification suppressed (already sent in last 5 mins) for user [ID: {$user->id}].");
                return;
            }

            Log::info("Lockout Listener: User found [ID: {$user->id}]. Dispatching notification.");
            $this->notificationService->dispatch(
                NotificationKeys::SECURITY_ALERT,
                $user,
                [
                    'subject' => 'Account Locked: Too Many Login Attempts',
                    'message' => 'Your account has been temporarily locked due to too many failed login attempts.',
                    'status'  => 'danger',
                    'icon'    => 'heroicon-o-lock-closed',
                    'action_text' => 'Review Security',
                    'action_url' => $this->getSecurityUrl($user),
                ]
            );
        }
    }

    /**
     * Get the appropriate security page URL based on user role.
     */
    protected function getSecurityUrl(User $user): string
    {
        if ($user->isAdmin()) {
            return route('filament.admin.pages.account.security');
        }

        if ($user->isTeacher()) {
            return route('filament.teacher.pages.account.security');
        }

        // Staff (Secretary, Accountant) now use the admin panel.
        if ($user->hasAnyRole(['secretary', 'accountant'])) {
            return route('filament.admin.pages.account.security');
        }

        // Fallback for users without a specific panel (e.g., students)
        return url('/');
    }
}
