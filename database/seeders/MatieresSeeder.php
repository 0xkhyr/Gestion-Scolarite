<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Matiere;

class MatieresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matieres = [
            [
                'nom_matiere' => 'Mathématiques',
                'code_matiere' => 'MATH',
                'description' => 'Mathématiques générales',
                'coefficient' => 4,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Français',
                'code_matiere' => 'FR',
                'description' => 'Langue française et littérature',
                'coefficient' => 3,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Anglais',
                'code_matiere' => 'ANG',
                'description' => 'Langue anglaise',
                'coefficient' => 2,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Histoire-Géographie',
                'code_matiere' => 'HG',
                'description' => 'Histoire et géographie',
                'coefficient' => 3,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Sciences Physiques',
                'code_matiere' => 'PHY',
                'description' => 'Physique et chimie',
                'coefficient' => 3,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Sciences de la Vie et de la Terre',
                'code_matiere' => 'SVT',
                'description' => 'Biologie et géologie',
                'coefficient' => 3,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Education Physique et Sportive',
                'code_matiere' => 'EPS',
                'description' => 'Sport et éducation physique',
                'coefficient' => 1,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Education Civique',
                'code_matiere' => 'EC',
                'description' => 'Education civique et morale',
                'coefficient' => 1,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Arts Plastiques',
                'code_matiere' => 'ART',
                'description' => 'Arts visuels et plastiques',
                'coefficient' => 1,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Informatique',
                'code_matiere' => 'INFO',
                'description' => 'Informatique et technologies',
                'coefficient' => 2,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Arabe',
                'code_matiere' => 'ARA',
                'description' => 'Langue arabe et littérature',
                'coefficient' => 4,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Éducation Islamique',
                'code_matiere' => 'ISL',
                'description' => 'Éducation religieuse islamique',
                'coefficient' => 2,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Philosophie',
                'code_matiere' => 'PHILO',
                'description' => 'Philosophie (lycée)',
                'coefficient' => 2,
                'active' => true,
            ],
        ];

        foreach ($matieres as $matiere) {
            Matiere::firstOrCreate(
                ['code_matiere' => $matiere['code_matiere']],
                $matiere
            );
        }
    }
}
