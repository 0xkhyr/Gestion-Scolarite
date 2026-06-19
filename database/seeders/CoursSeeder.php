<?php

namespace Database\Seeders;

use App\Models\Cours;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Matiere;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all teacher-subject-class assignments
        $assignments = DB::table('enseignant_matiere_classe')
                         ->where('enseignant_matiere_classe.active', true)
                         ->join('matieres', 'enseignant_matiere_classe.id_matiere', '=', 'matieres.id_matiere')
                         ->join('classes', 'enseignant_matiere_classe.id_classe', '=', 'classes.id_classe')
                         ->join('enseignants', 'enseignant_matiere_classe.id_enseignant', '=', 'enseignants.id_enseignant')
                         ->select(
                             'enseignant_matiere_classe.id_enseignant',
                             'enseignant_matiere_classe.id_matiere',
                             'enseignant_matiere_classe.id_classe',
                             'matieres.nom_matiere',
                             'classes.nom_classe'
                         )
                         ->get();

        if ($assignments->isEmpty()) {
            $this->command->warn('No teacher-subject-class assignments found. Run EnseignantMatiereClasseSeeder first.');
            return;
        }


        foreach ($assignments as $assignment) {
            // Create multiple course sessions per week for each assignment
            $weeklySchedules = $this->getWeeklySchedule($assignment->nom_matiere);
            
            foreach ($weeklySchedules as $schedule) {
                Cours::create([
                    'id_matiere' => $assignment->id_matiere,
                    'description' => "Cours de {$assignment->nom_matiere} pour la classe {$assignment->nom_classe}",
                    'id_classe' => $assignment->id_classe,
                    'id_enseignant' => $assignment->id_enseignant,
                    'jour' => $schedule['jour'],
                    'date_debut' => $schedule['date_debut'],
                    'date_fin' => $schedule['date_fin'],
                ]);
            }
        }

        // A handful of one-off makeup sessions (rattrapage) on real dates.
        $this->createRattrapageSessions($assignments);

        $coursCount = Cours::count();
        $this->command->info("{$coursCount} course sessions seeded.");
    }

    /** One-off dated sessions (date set) — makeup classes in the coming weeks. */
    private function createRattrapageSessions($assignments): void
    {
        foreach ($assignments->shuffle()->take(6) as $a) {
            $date = Carbon::now()->addDays(rand(3, 21));

            // Push to a teaching weekday (Sun–Thu in Mauritania; avoid Fri/Sat).
            while (in_array($date->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY], true)) {
                $date->addDay();
            }

            Cours::create([
                'id_matiere' => $a->id_matiere,
                'id_classe' => $a->id_classe,
                'id_enseignant' => $a->id_enseignant,
                'date' => $date->toDateString(),
                'date_debut' => '16:00',
                'date_fin' => '18:00',
                'description' => "Séance de rattrapage — {$a->nom_matiere} ({$a->nom_classe})",
            ]);
        }
    }

    private function getWeeklySchedule(string $matiere): array
    {
        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
        
        // Different subjects get different time allocations
        $subjectSchedules = [
            'Mathématiques' => ['sessions' => 3, 'duration' => 2],
            'Français' => ['sessions' => 3, 'duration' => 2],
            'Anglais' => ['sessions' => 2, 'duration' => 2],
            'Sciences Physiques' => ['sessions' => 2, 'duration' => 2],
            'Histoire-Géographie' => ['sessions' => 2, 'duration' => 1.5],
            'Éducation Physique' => ['sessions' => 2, 'duration' => 2],
            'Arts Plastiques' => ['sessions' => 1, 'duration' => 2],
        ];
        
        // Default schedule for unlisted subjects
        $schedule = $subjectSchedules[$matiere] ?? ['sessions' => 2, 'duration' => 1.5];
        
        $schedules = [];
        $usedJours = [];
        
        for ($i = 0; $i < $schedule['sessions']; $i++) {
            // Avoid duplicate days for the same subject
            $availableJours = array_diff($jours, $usedJours);
            if (empty($availableJours)) {
                break;
            }
            
            $jour = $availableJours[array_rand($availableJours)];
            $usedJours[] = $jour;
            
            // Generate realistic school hours
            $startTimes = ['08:00', '10:00', '13:30', '15:30'];
            $dateDebut = $startTimes[array_rand($startTimes)];
            
            // Calculate end time based on duration
            $endTime = date('H:i', strtotime($dateDebut . ' +' . ($schedule['duration'] * 60) . ' minutes'));
            
            $schedules[] = [
                'jour' => $jour,
                'date_debut' => $dateDebut,
                'date_fin' => $endTime,
            ];
        }
        
        return $schedules;
    }
}
