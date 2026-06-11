<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdministrateursSeeder::class,
            ClassesSeeder::class,
            MatieresSeeder::class,
            EnseignantsSeeder::class,
            EtudiantsSeeder::class,
            EnseignantMatiereClasseSeeder::class,
            CoursSeeder::class,
            EvaluationsSeeder::class,
            NotesSeeder::class,
            EtudePaiementSeeder::class,
            EnseignPaiementSeeder::class,
            DefaultPagesSeeder::class,
        ]);
    }
}
