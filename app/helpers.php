<?php

use App\Services\SettingsService;

if (!function_exists('setting')) {
    /**
     * Get or set a setting value
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed|SettingsService
     */
    function setting(string $key = null, $default = null)
    {
        $service = app(SettingsService::class);

        if ($key === null) {
            return $service;
        }

        return $service->get($key, $default);
    }
}

if (!function_exists('feature')) {
    /**
     * Is an optional module enabled? Modules are opt-in (default OFF).
     *
     * Backed by the `features.*` settings group and cached by the Setting model.
     * Single source of truth for gating navigation, settings sub-sections, and
     * enforcement behaviour.
     *
     * @example feature('attendance'), feature('submissions')
     */
    function feature(string $name): bool
    {
        return (bool) setting('features.' . $name, false);
    }
}
