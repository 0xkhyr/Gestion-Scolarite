<?php

namespace App\Http\Requests\Legacy;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
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
            'matiere' => ['required', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'type' => ['required', 'in:devoir,examen,controle'],
            'id_classe' => ['required', 'exists:classes,id_classe'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'matiere' => __('app.subject'),
            'date' => 'Date',
            'date_debut' => 'Date de début',
            'date_fin' => 'Date de fin',
            'type' => 'Type d\'évaluation',
            'id_classe' => __('app.class'),
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
            'type.in' => 'Le type d\'évaluation doit être : devoir, examen ou contrôle.',
            'id_classe.exists' => 'La classe sélectionnée n\'existe pas.',
        ];
    }
}
