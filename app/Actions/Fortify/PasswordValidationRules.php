<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Validation\Rule;
use App\Rules\PasswordComplexity;
use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', new PasswordComplexity(), 'confirmed'];
    }
}
