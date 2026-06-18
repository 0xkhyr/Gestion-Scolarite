<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get a setting value with proper priority: Database → ENV → Default
     */
    public static function get(string $key, $default = null)
    {
        // Check if Laravel is bootstrapped (facades are available)
        if (!app()->bound('cache') || !app()->bound('db')) {
            // During bootstrap, fall back to ENV then default
            $envKey = strtoupper(str_replace('.', '_', $key));
            $envValue = env($envKey);
            return $envValue !== null ? $envValue : $default;
        }

        try {
            return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
                // 1. Try database first (highest priority)
                try {
                    $setting = static::where('key', $key)->first();
                    
                    if ($setting) {
                        return static::castValue($setting->value, $setting->type);
                    }
                } catch (Exception $e) {
                    // If database isn't ready, fall back to ENV
                }

                // 2. Try environment variable (middle priority)
                $envKey = strtoupper(str_replace('.', '_', $key));
                $envValue = env($envKey);
                
                if ($envValue !== null) {
                    return $envValue;
                }

                // 3. Use default value (lowest priority)
                return $default;
            });
        } catch (Exception $e) {
            // If cache write fails (filesystem permission, disk full, etc.), avoid raising an exception
            logger()->warning('Settings cache write failed: ' . $e->getMessage());

            $envKey = strtoupper(str_replace('.', '_', $key));
            $envValue = env($envKey);

            return $envValue !== null ? $envValue : $default;
        }
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general', string $description = null): void
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => static::prepareValue($value, $type),
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );

        Cache::forget("setting.{$key}");
    }

    /**
     * Get all settings for a group
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("settings.group.{$group}", 3600, function () use ($group) {
            return static::where('group', $group)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [
                        $setting->key => static::castValue($setting->value, $setting->type)
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Cast value to proper type
     */
    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value, true),
            'array' => is_string($value) ? json_decode($value, true) : $value,
            default => $value,
        };
    }

    /**
     * Prepare value for storage
     */
    protected static function prepareValue($value, string $type): string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'json', 'array' => json_encode($value),
            default => (string) $value,
        };
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('settings');
        // Clear individual setting caches
        static::all()->each(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });
        // Clear group caches
        static::select('group')->distinct()->get()->each(function ($setting) {
            Cache::forget("settings.group.{$setting->group}");
        });
    }

    /**
     * Boot model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("setting.{$setting->key}");
            Cache::forget("settings.group.{$setting->group}");
        });

        static::deleted(function ($setting) {
            Cache::forget("setting.{$setting->key}");
            Cache::forget("settings.group.{$setting->group}");
        });
    }
}
