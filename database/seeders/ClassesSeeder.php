<?php

namespace Database\Seeders;

use App\Models\Classe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            // Maternelle (Preschool) - Ages 3-5
            ['nom_classe' => 'Petite Section', 'niveau' => 1],
            ['nom_classe' => 'Moyenne Section', 'niveau' => 2],
            ['nom_classe' => 'Grande Section', 'niveau' => 3],
            
            // Élémentaire (Primary) - Ages 6-10
            ['nom_classe' => 'CP', 'niveau' => 4],
            ['nom_classe' => 'CE1', 'niveau' => 5],
            ['nom_classe' => 'CE2', 'niveau' => 6],
            ['nom_classe' => 'CM1', 'niveau' => 7],
            ['nom_classe' => 'CM2', 'niveau' => 8],
            
            // Collège (Middle School) - Ages 11-14
            ['nom_classe' => '6ème A', 'niveau' => 9],
            ['nom_classe' => '6ème B', 'niveau' => 9],
            ['nom_classe' => '5ème A', 'niveau' => 10],
            ['nom_classe' => '5ème B', 'niveau' => 10],
            ['nom_classe' => '4ème A', 'niveau' => 11],
            ['nom_classe' => '4ème B', 'niveau' => 11],
            ['nom_classe' => '3ème A', 'niveau' => 12],
            ['nom_classe' => '3ème B', 'niveau' => 12],
            
            // Lycée (High School) - Ages 15-17
            ['nom_classe' => '2nde A', 'niveau' => 13],
            ['nom_classe' => '2nde B', 'niveau' => 13],
            ['nom_classe' => '1ère Littéraire', 'niveau' => 14],
            ['nom_classe' => '1ère Scientifique', 'niveau' => 14],
            ['nom_classe' => '1ère Économique', 'niveau' => 14],
            ['nom_classe' => 'Terminale L', 'niveau' => 15],
            ['nom_classe' => 'Terminale S', 'niveau' => 15],
            ['nom_classe' => 'Terminale ES', 'niveau' => 15],
        ];

        foreach ($classes as $classe) {
            Classe::create($classe);
        }
        
        $this->command->info(count($classes) . ' classes seeded.');
    }
}
