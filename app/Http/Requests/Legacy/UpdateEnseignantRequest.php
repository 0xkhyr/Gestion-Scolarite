<?php

namespace App\Http\Requests\Legacy;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEnseignantRequest extends FormRequest
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
        $enseignantId = $this->route('enseignant');
        
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'email', 
                Rule::unique('enseignants', 'email')->ignore($enseignantId, 'id_enseignant')
            ],
            'telephone' => ['required', 'string', 'max:20'],
            'matiere' => ['required', 'string', 'max:255'],
            'id_classe' => ['required', 'exists:classes,id_classe'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nom' => __('app.last_name'),
            'prenom' => __('app.first_name'),
            'email' => __('app.email'),
            'telephone' => __('app.phone'),
            'matiere' => __('app.subject'),
            'id_classe' => __('app.class'),
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'id_classe.exists' => 'La classe sélectionnée n\'existe pas.',
        ];
    }
}
