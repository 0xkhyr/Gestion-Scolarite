<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxFileSize implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Get max file size from settings (in MB)
        $maxSizeMB = setting('app.file_upload_max_size', 10);
        
        // Convert to bytes
        $maxSizeBytes = $maxSizeMB * 1024 * 1024;
        
        // Check if value is a file and get its size
        if ($value && method_exists($value, 'getSize')) {
            $fileSize = $value->getSize();
            
            if ($fileSize > $maxSizeBytes) {
                $fail("The {$attribute} may not be greater than {$maxSizeMB} MB.");
                return;
            }
        }
    }
}