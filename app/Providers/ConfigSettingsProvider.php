<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class ConfigSettingsProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only apply settings after the application is fully booted
        if ($this->app->bound('db') && $this->app->bound('cache')) {
            try {
                $this->applySettingsToConfig();
            } catch (Exception $e) {
                // Silently fail during testing or if database isn't ready
            }
        }
    }

    /**
     * Apply settings to configuration values
     */
    private function applySettingsToConfig(): void
    {
        // Update mail configuration with settings
        $schoolEmail = setting('school.email');
        $schoolName = setting('school.name');

        if ($schoolEmail) {
            Config::set('mail.from.address', $schoolEmail);
        }

        if ($schoolName) {
            Config::set('mail.from.name', $schoolName);
        }

        // Update session configuration with settings
        $sessionTimeout = setting('security.session_timeout');
        if ($sessionTimeout) {
            Config::set('session.lifetime', (int) $sessionTimeout);
        }

        // Enforce 2FA based on the admin setting (drives EnsureTwoFactorIsVerified).
        // Falls back to the existing config value, so nothing changes until opt-in.
        Config::set('security.require_2fa', (bool) setting(
            'security.two_factor_required',
            config('security.require_2fa', false)
        ));

        // Update Livewire file upload configuration
        $maxFileSize = setting('app.file_upload_max_size');
        if ($maxFileSize) {
            $maxSizeKB = (int) $maxFileSize * 1024;
            Config::set('livewire.temporary_file_upload.rules', function() use ($maxSizeKB) {
                return ['required', 'file', 'max:' . $maxSizeKB];
            });
        }

        // You can add more config updates here as needed
    }
}