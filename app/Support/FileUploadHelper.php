<?php

namespace App\Support;

use App\Rules\MaxFileSize;

class FileUploadHelper
{
    /**
     * Get file upload validation rules with settings
     */
    public static function getUploadRules(array $additionalRules = []): array
    {
        $rules = [
            'required',
            'file',
            new MaxFileSize(),
        ];
        
        // Add allowed file types if setting exists
        $allowedTypes = setting('allowed_file_types');
        if ($allowedTypes) {
            $mimeTypes = is_string($allowedTypes) ? explode(',', $allowedTypes) : $allowedTypes;
            $rules[] = 'mimes:' . implode(',', array_map('trim', $mimeTypes));
        }
        
        return array_merge($rules, $additionalRules);
    }
    
    /**
     * Get max file size for Filament FileUpload components (in KB)
     */
    public static function getMaxSizeKB(): int
    {
        return setting('app.file_upload_max_size', 10) * 1024;
    }
    
    /**
     * Get allowed mime types for Filament FileUpload components
     */
    public static function getAllowedTypes(): ?array
    {
        $allowedTypes = setting('allowed_file_types');
        
        if (!$allowedTypes) {
            return null;
        }
        
        if (is_string($allowedTypes)) {
            return array_map('trim', explode(',', $allowedTypes));
        }
        
        return $allowedTypes;
    }
    
    /**
     * Configure a Filament FileUpload component with settings
     */
    public static function configureFileUpload($component)
    {
        $component->maxSize(self::getMaxSizeKB());
        
        $allowedTypes = self::getAllowedTypes();
        if ($allowedTypes) {
            $component->acceptedFileTypes($allowedTypes);
        }
        
        return $component;
    }
}