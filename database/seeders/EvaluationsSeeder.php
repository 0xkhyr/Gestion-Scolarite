<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\Cours;
use App\Models\Classe;
use App\Models\Matiere;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EvaluationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active teacher-subject-class combinations
        $assignments = DB::table('enseignant_matiere_classe')
                         ->where('enseignant_matiere_classe.active', true)
                         ->join('matieres', 'enseignant_matiere_classe.id_matiere', '=', 'matieres.id_matiere')
                         ->join('classes', 'enseignant_matiere_classe.id_classe', '=', 'classes.id_classe')
                         ->select(
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


        // Create evaluations for each subject-class combination
        foreach ($assignments as $assignment) {
            // Create evaluations for different periods of the academic year
            $this->createTrimesterEvaluations($assignment, 1); // First trimester
            $this->createTrimesterEvaluations($assignment, 2); // Second trimester
            $this->createTrimesterEvaluations($assignment, 3); // Third trimester
        }

        $evaluationCount = Evaluation::count();
        $this->command->info("{$evaluationCount} evaluations seeded.");
    }
    
    private function createTrimesterEvaluations($assignment, int $trimester): void
    {
        $trimesterDates = $this->getTrimesterDates($trimester);
        
        // Create different types of evaluations per trimester
        $evaluationTypes = [
            ['type' => 'controle', 'count' => 2, 'max_note' => 20],
            ['type' => 'devoir', 'count' => 1, 'max_note' => 20],
        ];
        
        // Add exam for important subjects in trimester 3
        if ($trimester == 3 && $this->isMainSubject($assignment->nom_matiere)) {
            $evaluationTypes[] = ['type' => 'examen', 'count' => 1, 'max_note' => 20];
        }
        
        foreach ($evaluationTypes as $evalType) {
            for ($i = 0; $i < $evalType['count']; $i++) {
                $date = $this->getRandomDateInTrimester($trimesterDates);
                $evaluationDates = $this->getEvaluationTimes($evalType['type']);
                
                Evaluation::create([
                    'id_matiere' => $assignment->id_matiere,
                    'titre' => $this->generateEvaluationTitle($assignment->nom_matiere, $evalType['type'], $trimester, $i + 1),
                    'type' => $evalType['type'],
                    'date' => $date,
                    'date_debut' => $evaluationDates['date_debut'],
                    'date_fin' => $evaluationDates['date_fin'],
                    'id_classe' => $assignment->id_classe,
                    'note_max' => $evalType['max_note'],
                ]);
            }
        }
    }
    
    /**
     * Start year of the academic year we're currently in — so seeded
     * evaluations land in the live year (and show up on the calendar today).
     */
    private function academicStartYear(): int
    {
        $startMonth = (int) config('school.academic_year_start_month', 10);

        return (int) date('n') >= $startMonth ? (int) date('Y') : (int) date('Y') - 1;
    }

    private function getTrimesterDates(int $trimester): array
    {
        $y = $this->academicStartYear();

        switch ($trimester) {
            case 1:
                return [
                    'start' => Carbon::create($y, 10, 1),
                    'end' => Carbon::create($y, 12, 31)
                ];
            case 2:
                return [
                    'start' => Carbon::create($y + 1, 1, 1),
                    'end' => Carbon::create($y + 1, 4, 30)
                ];
            case 3:
                return [
                    'start' => Carbon::create($y + 1, 5, 1),
                    'end' => Carbon::create($y + 1, 7, 31)
                ];
            default:
                return [
                    'start' => Carbon::now()->subMonths(3),
                    'end' => Carbon::now()->subDays(1)
                ];
        }
    }
    
    private function getRandomDateInTrimester(array $dates): Carbon
    {
        $start = $dates['start']->timestamp;
        $end = $dates['end']->timestamp;
        
        // Avoid weekends
        do {
            $randomTimestamp = rand($start, $end);
            $date = Carbon::createFromTimestamp($randomTimestamp);
        } while ($date->isWeekend());
        
        return $date;
    }
    
    private function isMainSubject(string $subject): bool
    {
        $mainSubjects = [
            'Mathématiques', 'Français', 'Anglais', 'Arabe', 'Sciences Physiques',
            'Histoire-Géographie', 'Sciences de la Vie et de la Terre'
        ];
        
        return in_array($subject, $mainSubjects);
    }
    
    private function generateEvaluationTitle(string $matiere, string $type, int $trimester, int $numero): string
    {
        $typeNames = [
            'controle' => 'Contrôle',
            'devoir' => 'Devoir Surveillé',
            'examen' => 'Examen'
        ];
        
        $typeName = $typeNames[$type] ?? ucfirst($type);
        
        return "{$typeName} {$numero} - {$matiere} (T{$trimester})";
    }

    private function getEvaluationTimes(string $type): array
    {
        // Generate realistic evaluation times based on type
        $timeSlots = [
            'controle' => ['start' => '08:00', 'duration' => 60],  // 1 hour
            'devoir' => ['start' => '08:00', 'duration' => 120],   // 2 hours
            'examen' => ['start' => '08:00', 'duration' => 180],   // 3 hours
        ];
        
        $slot = $timeSlots[$type] ?? $timeSlots['controle'];
        
        $startTimes = ['08:00', '10:00', '14:00'];
        $startTime = $startTimes[array_rand($startTimes)];
        
        $endTime = date('H:i', strtotime($startTime . ' +' . $slot['duration'] . ' minutes'));
        
        return [
            'date_debut' => $startTime,
            'date_fin' => $endTime,
        ];
    }
}
