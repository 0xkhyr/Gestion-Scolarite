<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ThemeService
{
    /**
     * Get all theme variables for CSS generation
     */
    public function getThemeVars(): array
    {
        return Cache::remember('theme_vars', 3600, function () {
            return [
                'primary_color' => SiteSetting::primaryColor(),
                'secondary_color' => SiteSetting::secondaryColor(),
                'site_name' => SiteSetting::siteName(),
                'logo_url' => SiteSetting::logoUrl(),
                'contact_email' => SiteSetting::contactEmail(),
                'contact_phone' => SiteSetting::contactPhone(),
                'contact_address' => SiteSetting::contactAddress(),
            ];
        });
    }

    /**
     * Generate dynamic CSS based on theme settings
     */
    public function generateCSS(): string
    {
        $vars = $this->getThemeVars();
        
        return "
            :root {
                --primary-color: {$vars['primary_color']};
                --secondary-color: {$vars['secondary_color']};
            }
            
            .bg-primary {
                background-color: var(--primary-color);
            }
            
            .text-primary {
                color: var(--primary-color);
            }
            
            .border-primary {
                border-color: var(--primary-color);
            }
            
            .bg-secondary {
                background-color: var(--secondary-color);
            }
            
            .text-secondary {
                color: var(--secondary-color);
            }
            
            .hover\\:bg-primary:hover {
                background-color: var(--primary-color);
            }
            
            .hover\\:text-primary:hover {
                color: var(--primary-color);
            }
            
            /* Navigation styling */
            .navbar-brand {
                color: var(--primary-color);
                font-weight: 600;
            }
            
            .nav-link:hover {
                color: var(--primary-color);
            }
            
            /* Button styling */
            .btn-primary {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
            }
            
            .btn-primary:hover {
                background-color: var(--secondary-color);
                border-color: var(--secondary-color);
            }
            
            /* Form styling */
            .form-control:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
            }
        ";
    }

    /**
     * Upload and store logo file
     */
    public function uploadLogo(UploadedFile $file): string
    {
        // Delete old logo if exists
        $oldLogoUrl = SiteSetting::logoUrl();
        if ($oldLogoUrl && Storage::disk('public')->exists($oldLogoUrl)) {
            Storage::disk('public')->delete($oldLogoUrl);
        }

        // Store new logo
        $path = $file->store('logos', 'public');
        
        // Update setting
        SiteSetting::set('logo_url', $path, 'file', 'appearance');
        
        // Clear cache
        Cache::forget('theme_vars');
        
        return Storage::disk('public')->url($path);
    }

    /**
     * Get logo URL for display
     */
    public function getLogoUrl(): ?string
    {
        $logoPath = SiteSetting::logoUrl();
        
        if (!$logoPath) {
            return null;
        }
        
        if (Storage::disk('public')->exists($logoPath)) {
            return Storage::disk('public')->url($logoPath);
        }
        
        return null;
    }

    /**
     * Update theme colors
     */
    public function updateColors(string $primaryColor, string $secondaryColor): void
    {
        SiteSetting::set('primary_color', $primaryColor, 'text', 'appearance');
        SiteSetting::set('secondary_color', $secondaryColor, 'text', 'appearance');
        
        // Clear cache
        Cache::forget('theme_vars');
    }

    /**
     * Get default theme configuration
     */
    public function getDefaultTheme(): array
    {
        return [
            'primary_color' => '#4F6BED',
            'secondary_color' => '#3D55C8',
            'font_family' => 'Inter, system-ui, sans-serif',
            'logo_url' => '',
        ];
    }

    /**
     * Reset theme to defaults
     */
    public function resetToDefaults(): void
    {
        $defaults = $this->getDefaultTheme();
        
        foreach ($defaults as $key => $value) {
            SiteSetting::set($key, $value, 'text', 'appearance');
        }
        
        // Clear cache
        Cache::forget('theme_vars');
    }

    /**
     * Clear all theme cache
     */
    public function clearCache(): void
    {
        Cache::forget('theme_vars');
        Cache::forget('all_settings');
        Cache::tags(['theme'])->flush();
    }
}