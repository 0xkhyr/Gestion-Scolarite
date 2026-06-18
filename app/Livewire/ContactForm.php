<?php

namespace App\Livewire;

use Exception;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactForm extends Component
{
    public $name = '';
    public $email = '';
    public $subject = '';
    public $message = '';
    public $phone = '';
    
    public $showSuccess = false;
    public $showError = false;
    public $isSubmitting = false;

    protected $rules = [
        'name' => 'required|string|max:255|min:2',
        'email' => 'required|email:rfc,dns|max:255',
        'subject' => 'required|string|max:255|min:3',
        'message' => 'required|string|max:2000|min:10',
        'phone' => 'nullable|string|max:20|regex:/^[\+]?[\d\s\-\(\)]+$/',
    ];

    protected $messages = [
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

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        
        // Clear success/error states when user starts typing again
        if (in_array($propertyName, ['name', 'email', 'subject', 'message', 'phone'])) {
            $this->showSuccess = false;
            $this->showError = false;
        }
    }

    public function submit()
    {
        $this->isSubmitting = true;
        
        try {
            // Validate all fields
            $validatedData = $this->validate();
            
            // Clean data
            $cleanData = [
                'name' => trim($validatedData['name']),
                'email' => strtolower(trim($validatedData['email'])),
                'subject' => trim($validatedData['subject']),
                'message' => trim($validatedData['message']),
                'phone' => $validatedData['phone'] ? trim($validatedData['phone']) : null,
            ];

            // Basic spam check
            $spamWords = ['viagra', 'casino', 'lottery', 'bitcoin', 'crypto'];
            $content = strtolower($cleanData['message'] . ' ' . $cleanData['subject']);
            
            foreach ($spamWords as $word) {
                if (str_contains($content, $word)) {
                    $this->addError('message', 'Your message contains prohibited content.');
                    $this->isSubmitting = false;
                    return;
                }
            }

            // Store in database
            DB::table('contact_submissions')->insert([
                'name' => $cleanData['name'],
                'email' => $cleanData['email'],
                'subject' => $cleanData['subject'],
                'message' => $cleanData['message'],
                'phone' => $cleanData['phone'],
                'submitted_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log submission
            Log::info('Contact form submitted via Livewire', [
                'name' => $cleanData['name'],
                'email' => $cleanData['email'],
                'subject' => $cleanData['subject'],
                'ip' => request()->ip(),
            ]);

            // Clear form and show success
            $this->reset(['name', 'email', 'subject', 'message', 'phone']);
            $this->showSuccess = true;
            $this->showError = false;

        } catch (Exception $e) {
            Log::error('Contact form submission failed (Livewire)', [
                'error' => $e->getMessage(),
                'email' => $this->email ?? 'unknown',
                'trace' => $e->getTraceAsString(),
            ]);

            $this->showError = true;
            $this->showSuccess = false;
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
