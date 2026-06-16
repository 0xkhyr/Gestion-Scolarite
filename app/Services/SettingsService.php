<?php

namespace App\Services;

use App\Models\Setting;

/**
 * Settings Service
 * 
 * Manages application settings with consistent dot notation:
 * - school.*       (Organization/School settings)
 * - system.*       (System-wide configuration)
 * - academic.*     (Academic policies and grading)
 * - security.*     (Security and authentication)
 * - app.*          (Application preferences)
 * - notifications.* (Notification preferences)
 * 
 * All setting keys use dot notation for consistency with Laravel's config system.
 */
class SettingsService
{
    /**
     * School Settings - with ENV fallbacks
     */
    public function getSchoolSettings(): array
    {
        return Setting::getGroup('organization') + [
            'school.name' => env('SCHOOL_NAME', 'My School'),
            'school.address' => env('SCHOOL_ADDRESS', ''),
            'school.phone' => env('SCHOOL_PHONE', ''),
            'school.email' => env('SCHOOL_EMAIL', ''),
            'school.website' => env('SCHOOL_WEBSITE', ''),
            'school.location' => env('SCHOOL_LOCATION', ''),
            'school.latitude' => env('SCHOOL_LATITUDE', ''),
            'school.longitude' => env('SCHOOL_LONGITUDE', ''),
            'school.logo' => env('SCHOOL_LOGO', ''),
            'school.academic_year_start' => env('ACADEMIC_YEAR_START', '09-01'),
            'school.academic_year_end' => env('ACADEMIC_YEAR_END', '06-30'),
        ];
    }

    /**
     * System Settings - with ENV fallbacks
     */
    public function getSystemSettings(): array
    {
        return Setting::getGroup('system') + [
            'system.timezone' => env('APP_TIMEZONE', 'UTC'),
            'system.date_format' => env('DATE_FORMAT', 'Y-m-d'),
            'system.time_format' => env('TIME_FORMAT', 'H:i:s'),
            'system.language' => env('APP_LOCALE', 'en'),
            'system.currency' => env('CURRENCY', 'USD'),
            'system.currency_symbol' => env('CURRENCY_SYMBOL', '$'),
            'system.items_per_page' => (int) env('ITEMS_PER_PAGE', 25),
            'system.maintenance_mode' => env('APP_MAINTENANCE', false),
        ];
    }

    /**
     * Academic Settings - with ENV fallbacks
     */
    public function getAcademicSettings(): array
    {
        return Setting::getGroup('academic') + [
            // Mauritanian defaults: grading is /20, pass at 10, 3 trimesters.
            'academic.grading_system' => env('GRADING_SYSTEM', 'sur_20'),
            'academic.passing_grade' => (int) env('PASSING_GRADE', 10),
            'academic.max_grade' => (int) env('MAX_GRADE', 20),
            'academic.terms_per_year' => (int) env('TERMS_PER_YEAR', 3),
            'academic.attendance_required' => env('ATTENDANCE_REQUIRED', true),
            'academic.min_attendance_percentage' => (int) env('MIN_ATTENDANCE_PERCENTAGE', 75),
            'academic.late_submission_penalty' => (int) env('LATE_SUBMISSION_PENALTY', 10),
            'academic.max_absences_per_term' => (int) env('MAX_ABSENCES_PER_TERM', 5),
        ];
    }

    /**
     * Security Settings - with ENV fallbacks
     */
    public function getSecuritySettings(): array
    {
        return Setting::getGroup('security') + [
            'security.password_min_length' => (int) env('PASSWORD_MIN_LENGTH', 8),
            'security.password_require_uppercase' => env('PASSWORD_REQUIRE_UPPERCASE', true),
            'security.password_require_lowercase' => env('PASSWORD_REQUIRE_LOWERCASE', true),
            'security.password_require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),
            'security.password_require_symbols' => env('PASSWORD_REQUIRE_SYMBOLS', false),
            'security.session_timeout' => (int) env('SESSION_TIMEOUT', 120),
            'security.max_login_attempts' => (int) env('MAX_LOGIN_ATTEMPTS', 5),
            'security.lockout_duration' => (int) env('LOCKOUT_DURATION', 15),
            'security.two_factor_required' => env('TWO_FACTOR_REQUIRED', false),
            'security.password_expiry_enabled' => env('PASSWORD_EXPIRY_ENABLED', false),
            'security.password_expiry_days' => (int) env('PASSWORD_EXPIRY_DAYS', 90),
            'security.force_https' => env('FORCE_HTTPS', true),
        ];
    }

    /**
     * Application Settings - with ENV fallbacks
     */
    public function getApplicationSettings(): array
    {
        return Setting::getGroup('application') + [
            'app.name' => env('APP_NAME', config('app.name')),
            'app.version' => env('APP_VERSION', '1.0.0'),
            'app.default_user_role' => env('DEFAULT_USER_ROLE', 'student'),
            'app.registration_enabled' => env('REGISTRATION_ENABLED', true),
            'app.email_verification_required' => env('EMAIL_VERIFICATION_REQUIRED', true),
            'app.notifications_enabled' => env('NOTIFICATIONS_ENABLED', true),
            'app.file_upload_max_size' => (int) env('FILE_UPLOAD_MAX_SIZE', 10),
            'app.allowed_file_types' => ['pdf', 'doc', 'docx', 'jpg', 'png', 'gif'],
            'app.backup_frequency' => env('BACKUP_FREQUENCY', 'daily'),
            'app.auto_backup_enabled' => env('AUTO_BACKUP_ENABLED', true),
        ];
    }

    /**
     * Update school settings
     */
    public function updateSchoolSettings(array $data): void
    {
        foreach ($data as $key => $value) {
            $type = $this->getDataType($value);
            Setting::set($key, $value, $type, 'organization');
        }
    }

    /**
     * Update system settings
     */
    public function updateSystemSettings(array $data): void
    {
        foreach ($data as $key => $value) {
            $type = $this->getDataType($value);
            Setting::set($key, $value, $type, 'system');
        }
    }

    /**
     * Update academic settings
     */
    public function updateAcademicSettings(array $data): void
    {
        foreach ($data as $key => $value) {
            $type = $this->getDataType($value);
            Setting::set($key, $value, $type, 'academic');
        }
    }

    /**
     * Update security settings
     */
    public function updateSecuritySettings(array $data): void
    {
        foreach ($data as $key => $value) {
            $type = $this->getDataType($value);
            Setting::set($key, $value, $type, 'security');
        }
    }

    /**
     * Update application settings
     */
    public function updateApplicationSettings(array $data): void
    {
        foreach ($data as $key => $value) {
            $type = $this->getDataType($value);
            Setting::set($key, $value, $type, 'application');
        }
    }

    /**
     * Optional module flags - default OFF (opt-in). Gated via the feature() helper.
     */
    public function getModuleSettings(): array
    {
        return Setting::getGroup('features') + [
            'features.attendance' => env('FEATURE_ATTENDANCE', false),
            'features.submissions' => env('FEATURE_SUBMISSIONS', false),
        ];
    }

    /**
     * Update module flags.
     */
    public function updateModuleSettings(array $data): void
    {
        foreach ($data as $key => $value) {
            Setting::set($key, (bool) $value, 'boolean', 'features');
        }
    }

    /**
     * Get data type for setting value
     */
    private function getDataType($value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_int($value)) {
            return 'integer';
        }

        if (is_float($value)) {
            return 'float';
        }

        if (is_array($value)) {
            return 'json';
        }

        return 'string';
    }

    /**
     * Get a single setting
     */
    public function get(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }

    /**
     * Set a single setting
     */
    public function set(string $key, $value, string $group = 'general'): void
    {
        $type = $this->getDataType($value);
        Setting::set($key, $value, $type, $group);
    }

    /**
     * Reset settings to default for a group
     */
    public function resetGroup(string $group): void
    {
        Setting::where('group', $group)->delete();
        Setting::clearCache();
    }

    /**
     * Export all settings
     */
    public function exportSettings(): array
    {
        return Setting::all()->groupBy('group')->map(function ($settings) {
            return $settings->mapWithKeys(function ($setting) {
                return [$setting->key => [
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'description' => $setting->description,
                ]];
            });
        })->toArray();
    }

    /**
     * Import settings from array
     */
    public function importSettings(array $settings): void
    {
        foreach ($settings as $group => $groupSettings) {
            foreach ($groupSettings as $key => $data) {
                Setting::set(
                    $key,
                    $data['value'] ?? $data,
                    $data['type'] ?? 'string',
                    $group,
                    $data['description'] ?? null
                );
            }
        }
    }
}