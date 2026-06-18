<?php

namespace App\Notifications;

use Filament\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Support\NotificationChannels;
use Filament\Notifications\Notification as FilamentNotification;

/**
 * A generic, queued system notification that handles dynamic channels and data.
 * This class is a transport mechanism; business logic resides in NotificationService.
 */
class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $key;
    protected array $data;
    protected array $channels;

    /**
     * Create a new notification instance.
     *
     * @param string $key The notification key (from NotificationKeys)
     * @param array $data Data to be passed to the notification view/payload
     * @param array $channels List of channels to send to (subset of NotificationChannels constants)
     */
    public function __construct(string $key, array $data = [], array $channels = [])
    {
        $this->key = $key;
        $this->data = $data;
        $this->channels = $channels;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->data['subject'] ?? 'Notification System')
            ->line($this->data['message'] ?? 'You have a new notification.');

        if (!empty($this->data['action_text']) && !empty($this->data['action_url'])) {
            $message->action($this->data['action_text'], $this->data['action_url']);
        }

        return $message;
    }

    /**
     * Get the database representation of the notification.
     * Formatted specifically for Filament.
     */
    public function toDatabase(object $notifiable): array
    {
        $notification = FilamentNotification::make()
            ->title($this->data['subject'] ?? 'Notification')
            ->body($this->data['message'] ?? '')
            ->persistent();

        // Status / Color
        if (isset($this->data['status'])) {
            $notification->status($this->data['status']);
        }

        // Icon
        if (isset($this->data['icon'])) {
            $notification->icon($this->data['icon']);
        }

        // Action
        if (!empty($this->data['action_text']) && !empty($this->data['action_url'])) {
            $notification->actions([
                Action::make('view')
                    ->label($this->data['action_text'])
                    ->url($this->data['action_url'])
                    ->button(), // Optional: make it a button
            ]);
        }

        // Return the formatted array specifically for Filament
        // This includes 'format' => 'filament', etc.
        return array_merge(
            $notification->getDatabaseMessage(),
            ['key' => $this->key] // Keep our internal key for reference if needed
        );
    }

    /**
     * Get the array representation of the notification.
     * Fallback for other drivers or if toDatabase is not used (though toDatabase takes precedence for 'database' channel).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return array_merge([
            'key' => $this->key,
            'title' => $this->data['subject'] ?? 'Notification',
            'body' => $this->data['message'] ?? '',
        ], $this->data);
    }
}
