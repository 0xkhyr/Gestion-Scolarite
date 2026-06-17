<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

/**
 * Audit user logout. The logging user is still resolvable from the event.
 */
class LogLogout
{
    public function handle(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        activity('security')
            ->causedBy($event->user)
            ->performedOn($event->user)
            ->withProperties([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'type' => 'logout',
            ])
            ->log('Logout');
    }
}
