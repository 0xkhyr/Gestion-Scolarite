<?php

namespace App\Http\Requests\Legacy;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClasseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom_classe' => ['required', 'string', 'max:255', 'unique:classes,nom_classe'],
            'niveau' => ['required', 'string', 'max:255'],
            'nom_classe_translations' => ['nullable', 'array'],
            'nom_classe_translations.*.ar' => ['nullable', 'string', 'max:255'],
            'nom_classe_translations.*.fr' => ['nullable', 'string', 'max:255'],
            'niveau_translations' => ['nullable', 'array'],
            'niveau_translations.*.ar' => ['nullable', 'string', 'max:255'],
            'niveau_translations.*.fr' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nom_classe' => __('app.name'),
            'niveau' => __('app.niveau'),
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'nom_classe.unique' => 'Ce nom de classe existe déjà.',
        ];
    }
}
