<?php

namespace App\Http\Requests\Legacy;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEtudiantRequest extends FormRequest
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
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'date_naissance' => ['required', 'date', 'before:today'],
            'genre' => ['required', 'in:M,F'],
            'telephone' => ['required', 'string', 'max:20'],
            'adresse' => ['required', 'string', 'max:500'],
            'email' => ['nullable', 'email', 'unique:etudiants,email'],
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
            'date_naissance' => __('app.birth_date'),
            'genre' => __('app.gender'),
            'telephone' => __('app.phone'),
            'adresse' => __('app.address'),
            'email' => __('app.email'),
            'id_classe' => __('app.class'),
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'id_classe.exists' => 'La classe sélectionnée n\'existe pas.',
        ];
    }
}
