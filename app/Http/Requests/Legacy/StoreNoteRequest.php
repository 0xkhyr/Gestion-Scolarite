<?php

namespace App\Http\Requests\Legacy;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
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
            'note' => ['required', 'numeric', 'min:0', 'max:20'],
            'matiere' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'id_etudiant' => ['required', 'exists:etudiants,id_etudiant'],
            'id_evaluation' => ['required', 'exists:evaluations,id_evaluation'],
            'id_classe' => ['required', 'exists:classes,id_classe'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'note' => 'Note',
            'matiere' => __('app.subject'),
            'type' => 'Type',
            'id_etudiant' => 'Étudiant',
            'id_evaluation' => 'Évaluation',
            'id_classe' => __('app.class'),
            'commentaire' => 'Commentaire',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'note.max' => 'La note ne peut pas dépasser 20.',
            'note.min' => 'La note ne peut pas être inférieure à 0.',
            'id_etudiant.exists' => 'L\'étudiant sélectionné n\'existe pas.',
            'id_evaluation.exists' => 'L\'évaluation sélectionnée n\'existe pas.',
            'id_classe.exists' => 'La classe sélectionnée n\'existe pas.',
        ];
    }
}
