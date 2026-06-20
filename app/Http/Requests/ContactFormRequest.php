<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all users to submit contact forms
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'subject' => ['required', 'string', 'max:255', 'min:3'],
            'message' => ['required', 'string', 'max:2000', 'min:10'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[\d\s\-\(\)]+$/'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.min' => 'Your name must be at least 2 characters long.',
            'name.max' => 'Your name cannot exceed 255 characters.',
            
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Your email address cannot exceed 255 characters.',
            
            'subject.required' => 'Please enter a subject for your message.',
            'subject.min' => 'The subject must be at least 3 characters long.',
            'subject.max' => 'The subject cannot exceed 255 characters.',
            
            'message.required' => 'Please enter your message.',
            'message.min' => 'Your message must be at least 10 characters long.',
            'message.max' => 'Your message cannot exceed 2000 characters.',
            
            'phone.regex' => 'Please enter a valid phone number.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'email' => 'email address',
            'subject' => 'message subject',
            'message' => 'message content',
            'phone' => 'phone number',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and prepare data
        $this->merge([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'subject' => trim($this->subject),
            'message' => trim($this->message),
            'phone' => $this->phone ? trim($this->phone) : null,
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check for potential spam patterns
            $spamWords = ['viagra', 'casino', 'lottery', 'bitcoin', 'crypto'];
            $content = strtolower($this->message . ' ' . $this->subject);

            foreach ($spamWords as $word) {
                if (str_contains($content, $word)) {
                    $validator->errors()->add('message', 'Your message contains prohibited content.');
                    break;
                }
            }

            // Rate limiting check could be added here
            // For example, checking if the same email has submitted recently
        });
    }
}
