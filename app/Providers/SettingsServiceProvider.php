<?php

namespace App\Providers;

use Exception;
use DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Services\SettingsService;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole() || !$this->databaseExists()) {
            return;
        }

        try {
            $settingsService = app(SettingsService::class);
            
            // Load system settings into config
            $systemSettings = $settingsService->getSystemSettings();
            Config::set('app.timezone', $systemSettings['timezone']);
            
            // Load application settings
            $appSettings = $settingsService->getApplicationSettings();
            if ($appSettings['app_name'] !== config('app.name')) {
                Config::set('app.name', $appSettings['app_name']);
            }
            
            // Load security settings
            $securitySettings = $settingsService->getSecuritySettings();
            Config::set('session.lifetime', $securitySettings['session_timeout']);
            
        } catch (Exception $e) {
            // Handle gracefully if settings table doesn't exist yet
            logger('Settings loading failed: ' . $e->getMessage());
        }
    }

    /**
     * Check if database connection is available
     */
    private function databaseExists(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}