<?php

namespace App\Listeners;

use App\Models\Etudiant;
use App\Events\EvaluationCreated;
use App\Services\NotificationService;
use App\Support\NotificationKeys;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendEvaluationCreatedNotification implements ShouldQueue
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
    public function handle(EvaluationCreated $event): void
    {
        $evaluation = $event->evaluation;
        $classe = $evaluation->classe;

        if (!$classe) {
            return;
        }

        // Find all students in this class
        // Logic: Find users where profile_type is Etudiant and profile_id is in the class students
        $studentProfileIds = Etudiant::where('id_classe', $classe->id_classe)
            ->pluck('id_etudiant');

        $users = User::where('profile_type', Etudiant::class)
            ->whereIn('profile_id', $studentProfileIds)
            ->get();

        foreach ($users as $user) {
            $this->notificationService->dispatch(
                NotificationKeys::EVALUATION_CREATED,
                $user,
                [
                    'subject' => 'Nouvelle Évaluation : ' . ($evaluation->titre ?: $evaluation->type),
                    'message' => sprintf(
                        "Une nouvelle évaluation de %s (%s) a été programmée pour le %s.",
                        $evaluation->matiere_name,
                        $evaluation->type,
                        $evaluation->date->format('d/m/Y')
                    ),
                    'evaluation_id' => $evaluation->id_evaluation,
                    'status' => 'info',
                    'icon' => 'heroicon-o-academic-cap',
                    'action_text' => 'Voir les détails',
                    'action_url' => url('/student/evaluations'), // Adjust based on student panel if it exists
                ]
            );
        }
    }
}
