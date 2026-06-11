<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\Evaluation;
use App\Models\Etudiant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Controls
        $seedProfile = env('SEED_PROFILE', 'full'); // 'full' or 'light'
        $maxNotesPerEval = $seedProfile === 'full' ? null : (int) env('SEED_MAX_NOTES_PER_EVAL', 5);
        $maxNotesTotal = (int) (env('SEED_MAX_NOTES') ?? 0); // 0 = no limit
        $batchSize = (int) (env('SEED_BATCH_SIZE', 500));


        $totalInserted = 0;
        $batch = [];
        $stop = false;

        // Iterate evaluations in chunks to limit memory usage
        Evaluation::with('classe')->chunk(100, function ($evaluations) use (&$totalInserted, &$batch, &$stop, $maxNotesPerEval, $maxNotesTotal, $batchSize) {
            foreach ($evaluations as $evaluation) {
                if ($stop) {
                    return false; // stops chunking evaluations
                }

                // If there is already a large number of notes and a limit is set, stop
                if ($maxNotesTotal > 0 && $totalInserted >= $maxNotesTotal) {
                    $stop = true;
                    return false;
                }

                // Determine students (limited if configured)
                $studentsQuery = Etudiant::where('id_classe', $evaluation->id_classe);
                if (!is_null($maxNotesPerEval)) {
                    $studentsQuery->limit($maxNotesPerEval);
                }

                // Pre-fetch existing student notes for this evaluation to avoid duplicates
                $existing = Note::where('id_evaluation', $evaluation->id_evaluation)->pluck('id_etudiant')->all();

                $studentsQuery->chunk(100, function ($students) use ($evaluation, &$batch, &$totalInserted, &$stop, $existing, $maxNotesTotal, $batchSize) {
                    foreach ($students as $student) {
                        if ($stop) {
                            return false; // stop inner chunk
                        }

                        if ($maxNotesTotal > 0 && $totalInserted >= $maxNotesTotal) {
                            $stop = true;
                            return false;
                        }

                        if (in_array($student->id_etudiant, $existing, true)) {
                            continue; // already has a note
                        }

                        $note = $this->generateRealisticNote($evaluation->note_max, $evaluation->type);

                        $batch[] = [
                            'note' => $note,
                            'id_matiere' => $evaluation->id_matiere,
                            'type' => $evaluation->type,
                            'commentaire' => $this->generateComment($note, $evaluation->note_max, $evaluation->type),
                            'id_etudiant' => $student->id_etudiant,
                            'id_evaluation' => $evaluation->id_evaluation,
                            'id_classe' => $evaluation->id_classe,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $totalInserted++;

                        if (count($batch) >= $batchSize) {
                            DB::table('notes')->insert($batch);
                            $batch = [];
                        }
                    }

                    return true;
                });

                if ($stop) {
                    return false;
                }
            }

            return true;
        });

        // Insert remaining batch
        if (!empty($batch)) {
            DB::table('notes')->insert($batch);
        }

        $notesCount = Note::count();
        $this->command->info("{$notesCount} grades seeded ({$totalInserted} inserted this run).");
    }

    private function generateRealisticNote(int $noteMax, string $evaluationType): float
    {
        // Different grade distributions based on evaluation type and French grading system
        $distributions = [
            'controle' => [
                'excellent' => 0.12,  // 16-20 (12%)
                'good' => 0.25,       // 14-15 (25%)
                'average' => 0.35,    // 10-13 (35%)
                'below' => 0.28       // 0-9 (28%)
            ],
            'devoir' => [
                'excellent' => 0.10,  // 16-20 (10%)
                'good' => 0.22,       // 14-15 (22%)
                'average' => 0.38,    // 10-13 (38%)
                'below' => 0.30       // 0-9 (30%)
            ],
            'examen' => [
                'excellent' => 0.08,  // 16-20 (8%)
                'good' => 0.18,       // 14-15 (18%)
                'average' => 0.40,    // 10-13 (40%)
                'below' => 0.34       // 0-9 (34%)
            ]
        ];

        $dist = $distributions[$evaluationType] ?? $distributions['controle'];
        $rand = mt_rand() / mt_getrandmax();

        if ($rand < $dist['excellent']) {
            // Excellent: 16-20
            $min = $noteMax * 0.80;
            $max = $noteMax;
        } elseif ($rand < $dist['excellent'] + $dist['good']) {
            // Good: 14-15
            $min = $noteMax * 0.70;
            $max = $noteMax * 0.79;
        } elseif ($rand < $dist['excellent'] + $dist['good'] + $dist['average']) {
            // Average: 10-13 (passing grade)
            $min = $noteMax * 0.50;
            $max = $noteMax * 0.69;
        } else {
            // Below average: 0-9 (failing)
            $min = 0;
            $max = $noteMax * 0.49;
        }

        $note = $min + (mt_rand() / mt_getrandmax()) * ($max - $min);
        
        // Round to 0.25 precision (French system: 12.25, 12.5, 12.75, etc.)
        return round($note * 4) / 4;
    }

    private function generateComment(float $note, int $noteMax, string $type): string
    {
        $percentage = ($note / $noteMax) * 100;
        
        // French academic comments based on performance
        if ($percentage >= 80) {
            $comments = [
                'Excellent travail, à continuer ainsi !',
                'Très bonne maîtrise des notions.',
                'Performance remarquable.',
                'Très bien, félicitations !',
                'Excellente compréhension du sujet.'
            ];
        } elseif ($percentage >= 70) {
            $comments = [
                'Bon travail, quelques points à améliorer.',
                'Bonne performance générale.',
                'Bien, continuez vos efforts.',
                'Bonnes connaissances, à approfondir.',
                'Satisfaisant, peut mieux faire.'
            ];
        } elseif ($percentage >= 50) {
            $comments = [
                'Travail moyen, des efforts à fournir.',
                'Assez bien, amélioration nécessaire.',
                'Acquis fragiles, revoir les bases.',
                'Peut mieux faire, travaillez davantage.',
                'Résultats moyens, plus de révision nécessaire.'
            ];
        } else {
            $comments = [
                'Travail insuffisant, revoir les leçons.',
                'Difficultés importantes, aide nécessaire.',
                'Notions non acquises, rattrapage indispensable.',
                'Résultats préoccupants, soutien requis.',
                'Travail à reprendre entièrement.'
            ];
        }
        
        $baseComment = $comments[array_rand($comments)];
        
        // Add specific comments for evaluation type
        if ($type === 'examen') {
            $baseComment .= ' (Examen final)';
        } elseif ($type === 'devoir' && $percentage < 50) {
            $baseComment .= ' Préparez-vous mieux pour le prochain devoir.';
        }
        
        return $baseComment;
    }
}
