<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Listeners\UpdateLastLoginAt;
use App\Listeners\SendLockoutNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            UpdateLastLoginAt::class,
        ],
        \Illuminate\Auth\Events\Logout::class => [
            \App\Listeners\LogLogout::class,
        ],
        Lockout::class => [
            SendLockoutNotification::class,
        ],
        \App\Events\GradePublished::class => [
            \App\Listeners\SendGradePublishedNotification::class,
        ],
        \App\Events\EvaluationCreated::class => [
            \App\Listeners\SendEvaluationCreatedNotification::class,
        ],
        \App\Events\StudentPaymentReceived::class => [
            \App\Listeners\SendStudentPaymentNotification::class,
        ],
        \App\Events\TeacherPaymentProcessed::class => [
            \App\Listeners\SendTeacherPaymentNotification::class,
        ],

        // Audit role/permission assignment changes (security-critical).
        \Spatie\Permission\Events\RoleAttached::class => [
            [\App\Listeners\LogPermissionChange::class, 'handleRoleAttached'],
        ],
        \Spatie\Permission\Events\RoleDetached::class => [
            [\App\Listeners\LogPermissionChange::class, 'handleRoleDetached'],
        ],
        \Spatie\Permission\Events\PermissionAttached::class => [
            [\App\Listeners\LogPermissionChange::class, 'handlePermissionAttached'],
        ],
        \Spatie\Permission\Events\PermissionDetached::class => [
            [\App\Listeners\LogPermissionChange::class, 'handlePermissionDetached'],
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register the event listeners.
     *
     * Laravel 11 auto-registers the framework's base EventServiceProvider, which
     * discovers listeners in app/Listeners and registers them as `Class@method`.
     * This app declares every listener explicitly in $listen above, so leaving
     * discovery on registers each listener twice (once as `Class`, once as
     * `Class@method`) and fires it twice. Disable discovery globally so only the
     * explicit $listen map applies.
     */
    public function register(): void
    {
        parent::register();

        self::disableEventDiscovery();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
