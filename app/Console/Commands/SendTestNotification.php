<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Models\User;
use App\Services\NotificationService;
use App\Support\NotificationKeys;

class SendTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:test {email? : The email of the user to notify}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test notification to a user to verify the system';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $email = $this->argument('email');

        if (!$email) {
            // Default to the first admin or user if not provided
            $user = User::first();
            if (!$user) {
                $this->error('No users found in the database.');
                return;
            }
        } else {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("User with email {$email} not found.");
                return;
            }
        }

        $this->info("Sending test notification to: {$user->email} ({$user->name})");

        try {
            $notificationService->dispatch(
                NotificationKeys::SYSTEM_UPDATE, // Using a generic key or create a TEST key
                $user,
                [
                    'subject' => 'Test Notification System',
                    'message' => 'This is a test notification to verify the Filament integration is working correctly.',
                    'action_text' => 'View Dashboard',
                    'action_url' => url('/admin'),
                    'status' => 'success',
                ]
            );

            $this->info('Notification dispatched successfully!');
            $this->info('Check the Filament UI bell icon.');

        } catch (Exception $e) {
            $this->error("Failed to prompt notification: " . $e->getMessage());
        }
    }
}
