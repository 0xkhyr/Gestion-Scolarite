<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enseignant;
use App\Models\Classe;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EnseignantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enseignants = [
            [
                'nom' => 'El Moctar',
                'prenom' => 'Sidi Mohamed',
                'telephone' => '+222 20 11 22 33',
                'adresse' => 'Dar Naim, Nouakchott',
                'email' => 'elmoctar@ecole.com',
                'password' => 'teacher123',
            ],
            [
                'nom' => 'Mint Mohamedou',
                'prenom' => 'Aminetou',
                'telephone' => '+222 20 11 22 34',
                'adresse' => 'Tevragh Zeina, Nouakchott',
                'email' => 'aminetou@ecole.com',
                'password' => 'teacher123',
            ],
            [
                'nom' => 'Ould Baba',
                'prenom' => 'Oumar',
                'telephone' => '+222 20 11 22 35',
                'adresse' => 'Ksar, Nouakchott',
                'email' => 'oumar@ecole.com',
                'password' => 'teacher123',
            ],
            [
                'nom' => 'Mint Ahmed',
                'prenom' => 'Khadija',
                'telephone' => '+222 20 11 22 36',
                'adresse' => 'El Mina, Nouakchott',
                'email' => 'khadija@ecole.com',
                'password' => 'teacher123',
            ],
            [
                'nom' => 'Ould Sid Ahmed',
                'prenom' => 'Mohamed Lemine',
                'telephone' => '+222 20 11 22 37',
                'adresse' => 'Sebkha, Nouakchott',
                'email' => 'lemine@ecole.com',
                'password' => 'teacher123',
            ],
            [
                'nom' => 'Mint Cheikh',
                'prenom' => 'Mariem',
                'telephone' => '+222 20 11 22 38',
                'adresse' => 'Arafat, Nouakchott',
                'email' => 'mariem@ecole.com',
                'password' => 'teacher123',
            ],
            [
                'nom' => 'Ould Abdallahi',
                'prenom' => 'Cheikh',
                'telephone' => '+222 20 11 22 39',
                'adresse' => 'Toujounine, Nouakchott',
                'email' => 'cheikh@ecole.com',
                'password' => 'teacher123',
            ],
            [
                'nom' => 'Mint Vall',
                'prenom' => 'Zeynab',
                'telephone' => '+222 20 11 22 40',
                'adresse' => 'Riad, Nouakchott',
                'email' => 'zeynab@ecole.com',
                'password' => 'teacher123',
            ],
            [
                'nom' => 'Ould Mohamed',
                'prenom' => 'Brahim',
                'telephone' => '+222 20 11 22 41',
                'adresse' => 'Medina, Nouakchott',
                'email' => 'brahim@ecole.com',
                'password' => 'teacher123',
            ],
            [
                'nom' => 'Mint Yahya',
                'prenom' => 'Aïcha',
                'telephone' => '+222 20 11 22 42',
                'adresse' => 'Ksar, Nouakchott',
                'email' => 'aicha@ecole.com',
                'password' => 'teacher123',
            ],
        ];

        foreach ($enseignants as $data) {
            // Skip if user already exists to make seeder idempotent
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser) {
                $this->command->info('Skipping existing user: ' . $data['email']);
                if (!$existingUser->hasRole('teacher')) {
                    $existingUser->assignRole('teacher');
                    $this->command->info('Assigned missing role teacher to ' . $data['email']);
                }
                continue;
            }

            // Create Enseignant profile record
            $enseignant = Enseignant::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'telephone' => $data['telephone'],
                'adresse' => $data['adresse'],
            ]);

            // Create User account with polymorphic relationship
            $user = User::create([
                'name' => trim($data['prenom'] . ' ' . $data['nom']),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => true,
                'email_verified_at' => now(),
                'profile_type' => Enseignant::class,
                'profile_id' => $enseignant->id_enseignant,
            ]);

            // Assign teacher role using Spatie
            $user->assignRole('teacher');
        }
    }
}