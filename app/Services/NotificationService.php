<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\NotificationPreference;
use App\Models\NotificationLog;
use App\Notifications\SystemNotification;
use App\Support\NotificationChannels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Service to handle all business logic regarding notifications.
 * Enforces preference checks and logging.
 */
class NotificationService
{
    /**
     * Dispatch a notification to a specific user.
     * 
     * @param string $key The notification key
     * @param User $user The recipient
     * @param array $data Data for email/db/etc (subject, message, action_url, etc)
     * @return void
     */
    public function dispatch(string $key, User $user, array $data = []): void
    {
        $allowedChannels = $this->channelsFor($user, $key);

        if (empty($allowedChannels)) {
            Log::info("Notification [{$key}] suppressed for user [{$user->id}] due to preferences.");
            return;
        }

        try {
            // Send the notification using Queue
            $user->notify(new SystemNotification($key, $data, $allowedChannels));
            
            // Log success for each channel
            foreach ($allowedChannels as $channel) {
                $this->logNotification($user, $key, $channel, 'sent', $data);
            }

        } catch (Exception $e) {
            Log::error("Failed to dispatch notification [{$key}] to user [{$user->id}]: " . $e->getMessage());
            
            // Log failure
            foreach ($allowedChannels as $channel) {
                $this->logNotification($user, $key, $channel, 'failed', $data, $e->getMessage());
            }
        }
    }

    /**
     * Check if a specific channel is enabled for a user and key.
     * Default to true if no preference record exists (Opt-out model) 
     * or false (Opt-in model). Let's assume Opt-out (default true) for critical, 
     * but we'll stick to 'true' default for now as implied by migration `default(true)`.
     */
    public function isEnabled(User $user, string $key, string $channel): bool
    {
        $preference = NotificationPreference::where('user_id', $user->id)
            ->where('key', $key)
            ->where('channel', $channel)
            ->first();

        // If no preference found, return default enabled state (true)
        // You can change this policy based on strict requirements.
        return $preference ? $preference->enabled : true;
    }

    /**
     * Determine which channels are allowed for this notification.
     * 
     * @return array
     */
    public function channelsFor(User $user, string $key): array
    {
        $allChannels = NotificationChannels::all();
        $allowed = [];

        foreach ($allChannels as $channel) {
            // Some keys might simply not support certain channels (e.g. SMS for non-critical)
            // You could add logic here to filter channels by key BEFORE checking user preferences.
            
            if ($this->isEnabled($user, $key, $channel)) {
                $allowed[] = $channel;
            }
        }

        return $allowed;
    }

    protected function logNotification(User $user, string $key, string $channel, string $status, array $payload, ?string $error = null): void
    {
        NotificationLog::create([
            'user_id' => $user->id,
            'key' => $key,
            'channel' => $channel,
            'status' => $status,
            'payload' => $payload,
            'error' => $error,
        ]);
    }
}
