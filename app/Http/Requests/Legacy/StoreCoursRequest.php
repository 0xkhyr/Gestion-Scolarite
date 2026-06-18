<?php

namespace App\Http\Requests\Legacy;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCoursRequest extends FormRequest
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
            'jour' => ['required', 'in:lundi,mardi,mercredi,jeudi,vendredi,samedi,dimanche'],
            'date_debut' => ['required', 'date_format:H:i'],
            'date_fin' => ['required', 'date_format:H:i', 'after:date_debut'],
            'matiere' => ['required', 'string', 'max:255'],
            'id_enseignant' => ['required', 'exists:enseignants,id_enseignant'],
            'id_classe' => ['required', 'exists:classes,id_classe'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'jour' => 'Jour',
            'date_debut' => 'Heure de début',
            'date_fin' => 'Heure de fin',
            'matiere' => __('app.subject'),
            'id_enseignant' => 'Enseignant',
            'id_classe' => __('app.class'),
            'description' => 'Description',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'date_fin.after' => 'L\'heure de fin doit être après l\'heure de début.',
            'id_enseignant.exists' => 'L\'enseignant sélectionné n\'existe pas.',
            'id_classe.exists' => 'La classe sélectionnée n\'existe pas.',
        ];
    }
}
