<?php

namespace App\Listeners;

use App\Services\SettingsService;
use App\Events\TeacherPaymentProcessed;
use App\Services\NotificationService;
use App\Support\NotificationKeys;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTeacherPaymentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(TeacherPaymentProcessed $event): void
    {
        // Check if teacher payment notifications are enabled system-wide
        $settingsService = app(SettingsService::class);
        if (!$settingsService->get('notifications.teacher_payment.enabled', true)) {
            return;
        }

        $payment = $event->payment;
        $user = $payment->enseignant;

        if ($user && !$payment->notified) {
            $this->notificationService->dispatch(
                NotificationKeys::TEACHER_PAYMENT_PROCESSED,
                $user,
                [
                    'subject' => 'Versement de Salaire / Paiement Effectué',
                    'message' => sprintf(
                        "Votre paiement de %s (%s) pour '%s' a été traité avec succès le %s.",
                        number_format($payment->montant, 2, ',', ' '),
                        config('app.currency', 'FCFA'),
                        $payment->typepaiement,
                        $payment->date_paiement->format('d/m/Y')
                    ),
                    'payment_id' => $payment->id_paiements,
                    'status' => 'success',
                    'icon' => 'heroicon-o-banknotes',
                    'action_text' => 'Consulter mes paiements',
                    'action_url' => url('/teacher/payments'),
                ]
            );
            
            // Mark as notified to prevent duplicates
            $payment->update(['notified' => true]);
        }
    }
}
