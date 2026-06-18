<?php

namespace App\Listeners;

use App\Models\Etudiant;
use App\Events\StudentPaymentReceived;
use App\Services\NotificationService;
use App\Support\NotificationKeys;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendStudentPaymentNotification implements ShouldQueue
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
    public function handle(StudentPaymentReceived $event): void
    {
        $payment = $event->payment;
        $etudiant = $payment->etudiant;

        if (!$etudiant) {
            return;
        }

        // Find the user associated with this student
        $user = User::where('profile_type', Etudiant::class)
            ->where('profile_id', $etudiant->id_etudiant)
            ->first();

        if ($user) {
            $this->notificationService->dispatch(
                NotificationKeys::STUDENT_PAYMENT_RECEIVED,
                $user,
                [
                    'subject' => 'Confirmation de Paiement Reçu',
                    'message' => sprintf(
                        "Merci ! Votre paiement de %s (%s) pour '%s' a bien été reçu le %s.",
                        number_format($payment->montant, 2, ',', ' '),
                        config('app.currency', 'FCFA'),
                        $payment->typepaye,
                        $payment->date_paiement->format('d/m/Y')
                    ),
                    'payment_id' => $payment->id_paiements,
                    'status' => 'success',
                    'icon' => 'heroicon-o-check-circle',
                    'action_text' => 'Voir l\'historique',
                    'action_url' => url('/student/payments'), 
                ]
            );
        }
    }
}
