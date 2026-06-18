<?php

namespace App\Providers;

use Illuminate\Auth\Events\Logout;
use App\Listeners\LogLogout;
use App\Events\GradePublished;
use App\Listeners\SendGradePublishedNotification;
use App\Events\EvaluationCreated;
use App\Listeners\SendEvaluationCreatedNotification;
use App\Events\StudentPaymentReceived;
use App\Listeners\SendStudentPaymentNotification;
use App\Events\TeacherPaymentProcessed;
use App\Listeners\SendTeacherPaymentNotification;
use Spatie\Permission\Events\RoleAttached;
use App\Listeners\LogPermissionChange;
use Spatie\Permission\Events\RoleDetached;
use Spatie\Permission\Events\PermissionAttached;
use Spatie\Permission\Events\PermissionDetached;
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
        Logout::class => [
            LogLogout::class,
        ],
        Lockout::class => [
            SendLockoutNotification::class,
        ],
        GradePublished::class => [
            SendGradePublishedNotification::class,
        ],
        EvaluationCreated::class => [
            SendEvaluationCreatedNotification::class,
        ],
        StudentPaymentReceived::class => [
            SendStudentPaymentNotification::class,
        ],
        TeacherPaymentProcessed::class => [
            SendTeacherPaymentNotification::class,
        ],

        // Audit role/permission assignment changes (security-critical).
        RoleAttached::class => [
            [LogPermissionChange::class, 'handleRoleAttached'],
        ],
        RoleDetached::class => [
            [LogPermissionChange::class, 'handleRoleDetached'],
        ],
        PermissionAttached::class => [
            [LogPermissionChange::class, 'handlePermissionAttached'],
        ],
        PermissionDetached::class => [
            [LogPermissionChange::class, 'handlePermissionDetached'],
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
