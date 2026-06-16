<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'text', string $group = 'general'): void
    {
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) || is_object($value) ? json_encode($value) : $value,
                'type' => $type,
                'group' => $group,
            ]
        );

        Cache::forget("setting_{$key}");
        Cache::forget('all_settings');
    }

    /**
     * Get all settings grouped
     */
    public static function getAllGrouped(): array
    {
        return Cache::remember('all_settings', 3600, function () {
            $settings = parent::all(); // Use parent::all() to call Eloquent's all() method
            $grouped = [];

            foreach ($settings as $setting) {
                $grouped[$setting->group][$setting->key] = self::castValue($setting->value, $setting->type);
            }

            return $grouped;
        });
    }

    /**
     * Get settings by group
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
            $settings = self::where('group', $group)->get();
            $result = [];

            foreach ($settings as $setting) {
                $result[$setting->key] = self::castValue($setting->value, $setting->type);
            }

            return $result;
        });
    }

    /**
     * Cast value based on type
     */
    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            'integer' => (int) $value,
            'float' => (float) $value,
            default => $value,
        };
    }

    /**
     * Clear cache when saving
     */
    protected static function booted()
    {
        static::saved(function ($setting) {
            Cache::forget("setting_{$setting->key}");
            Cache::forget('all_settings');
            Cache::forget("settings_group_{$setting->group}");
        });

        static::deleted(function ($setting) {
            Cache::forget("setting_{$setting->key}");
            Cache::forget('all_settings');
            Cache::forget("settings_group_{$setting->group}");
        });
    }

    /**
     * Helper methods for common settings
     */
    public static function siteName(): string
    {
        return self::get('site_name', 'School Management System');
    }

    public static function siteDescription(): string
    {
        return self::get('site_description', 'Excellence in Education');
    }

    public static function contactEmail(): string
    {
        return self::get('contact_email', 'info@school.com');
    }

    public static function contactPhone(): string
    {
        return self::get('contact_phone', '');
    }

    public static function contactAddress(): string
    {
        return self::get('contact_address', '');
    }

    public static function primaryColor(): string
    {
        return self::get('primary_color', '#4F6BED');
    }

    public static function secondaryColor(): string
    {
        return self::get('secondary_color', '#3D55C8');
    }

    public static function logoUrl(): string
    {
        return self::get('logo_url', '');
    }
}
