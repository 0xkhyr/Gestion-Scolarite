<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\Classe;

class EnseignantMatiereClasseSeeder extends Seeder
{
    /**
     * Assign teachers → subjects → class groups, driven by each class's cycle.
     * Each subject has a "specialist" teacher (round-robin over the staff), who
     * then teaches that subject in every class group of the relevant cycle.
     */
    public function run(): void
    {
        $teachers = Enseignant::orderBy('id_enseignant')->get();
        $matieres = Matiere::all()->keyBy('code_matiere');
        $classes = Classe::all();

        if ($teachers->isEmpty() || $matieres->isEmpty() || $classes->isEmpty()) {
            $this->command->warn('Missing teachers, subjects, or classes — seed those first.');
            return;
        }

        // Subjects taught at each cycle (Mauritanian curriculum, simplified).
        $cycleSubjects = [
            'fondamental' => ['ARA', 'FR', 'MATH', 'ISL', 'EPS', 'ART'],
            'college'     => ['ARA', 'FR', 'ANG', 'MATH', 'PHY', 'SVT', 'HG', 'ISL', 'EPS', 'INFO'],
            'lycee'       => ['ARA', 'FR', 'ANG', 'MATH', 'PHY', 'SVT', 'HG', 'ISL', 'PHILO', 'INFO'],
        ];

        // A specialist teacher per subject code (spread across the staff).
        $allCodes = collect($cycleSubjects)->flatten()->unique()->values();
        $subjectTeacher = [];
        foreach ($allCodes as $i => $code) {
            $subjectTeacher[$code] = $teachers[$i % $teachers->count()];
        }

        $now = now();
        $rows = [];

        foreach ($classes as $classe) {
            $codes = $cycleSubjects[$classe->cycle] ?? $cycleSubjects['college'];

            foreach ($codes as $code) {
                $matiere = $matieres->get($code);
                if (! $matiere) {
                    continue;
                }

                $rows[] = [
                    'id_enseignant' => $subjectTeacher[$code]->id_enseignant,
                    'id_matiere' => $matiere->id_matiere,
                    'id_classe' => $classe->id_classe,
                    'active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('enseignant_matiere_classe')->insertOrIgnore($chunk);
        }

        $total = DB::table('enseignant_matiere_classe')->count();
        $this->command->info("{$teachers->count()} teachers, {$total} subject-class assignments seeded.");
    }
}
