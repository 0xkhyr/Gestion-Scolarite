<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Automated backups (spatie/laravel-backup)
|--------------------------------------------------------------------------
| Gated on the admin setting app.auto_backup_enabled (the ->when() runs at
| schedule time). Cadence comes from app.backup_frequency. Requires OS cron
| to run `php artisan schedule:run` every minute (see .docs/SETTINGS_WIRING_PLAN.md).
*/
$backupEnabled = fn (): bool => (bool) setting('app.auto_backup_enabled', false);

// Prune old backups daily (no-op cost when disabled).
Schedule::command('backup:clean')
    ->daily()->at('01:00')
    ->when($backupEnabled);

$backup = Schedule::command('backup:run')->when($backupEnabled);

// frequency read at definition time; falls back to default if DB not ready.
match (setting('app.backup_frequency', 'daily')) {
    'weekly' => $backup->weeklyOn(1, '02:00'),
    'monthly' => $backup->monthlyOn(1, '02:00'),
    default => $backup->dailyAt('02:00'),
};
