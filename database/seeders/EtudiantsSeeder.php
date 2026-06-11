<?php

namespace Database\Seeders;

use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EtudiantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = Classe::all();
        
        if ($classes->isEmpty()) {
            $this->command->warn('No classes found. Run ClassesSeeder first.');
            return;
        }

        // Sample of realistic students for demonstration
        $this->createSampleStudents($classes);
        
        // Generate additional random students for each class
        $this->generateRandomStudents($classes);
        
        $this->command->info(Etudiant::count() . ' students seeded.');
    }
    
    private function createSampleStudents($classes): void
    {
        $sampleStudents = [
            // CP Students (Age 6-7)
            [
                'nom' => 'Ba',
                'prenom' => 'Aissata',
                'telephone' => '+222 30 11 22 33',
                'adresse' => 'Tevragh Zeina, Quartier 5, Nouakchott',
                'date_naissance' => '2018-03-15',
                'genre' => 'F',
                'classe' => 'CP',
                'has_account' => false,
            ],
            [
                'nom' => 'Ould Ahmed',
                'prenom' => 'Mohamed Salem',
                'telephone' => '+222 30 11 22 34', 
                'adresse' => 'Ksar, Rue 42-156, Nouakchott',
                'date_naissance' => '2017-11-22',
                'genre' => 'M',
                'classe' => 'CP',
                'has_account' => false,
            ],
            
            // CM2 Students (Age 10-11)
            [
                'nom' => 'Mint Sidi',
                'prenom' => 'Fatimata',
                'telephone' => '+222 30 11 22 35',
                'adresse' => 'El Mina, Bloc B, Nouakchott',
                'date_naissance' => '2013-08-12',
                'genre' => 'F',
                'classe' => 'CM2',
                'has_account' => false,
            ],
            [
                'nom' => 'Diallo',
                'prenom' => 'Amadou',
                'telephone' => '+222 30 11 22 36',
                'adresse' => 'Medina, Secteur 3, Nouakchott',
                'date_naissance' => '2013-04-25',
                'genre' => 'M',
                'classe' => 'CM2',
                'has_account' => true,
                'email' => 'amadou.diallo@student.ecole.com',
            ],
            
            // 6ème Students (Age 11-12)
            [
                'nom' => 'Sy',
                'prenom' => 'Mariama',
                'telephone' => '+222 30 11 22 37',
                'adresse' => 'Arafat, Ilot K, Nouakchott',
                'date_naissance' => '2012-05-30',
                'genre' => 'F',
                'classe' => '6ème A',
                'has_account' => true,
                'email' => 'mariama.sy@student.ecole.com',
            ],
            [
                'nom' => 'Ould Baba',
                'prenom' => 'Ahmed Mahmoud',
                'telephone' => '+222 30 11 22 38',
                'adresse' => 'Sebkha, Secteur 12, Nouakchott',
                'date_naissance' => '2012-01-12',
                'genre' => 'M',
                'classe' => '6ème B',
                'has_account' => true,
                'email' => 'ahmed.ouldbaba@student.ecole.com',
            ],
            
            // Terminale Students (Age 17-18)
            [
                'nom' => 'Kane',
                'prenom' => 'Ousmane',
                'telephone' => '+222 30 11 22 39',
                'adresse' => 'Riad, Villa 245, Nouakchott',
                'date_naissance' => '2007-12-10',
                'genre' => 'M',
                'classe' => 'Terminale S',
                'has_account' => true,
                'email' => 'ousmane.kane@student.ecole.com',
            ],
            [
                'nom' => 'Mint Ebnou',
                'prenom' => 'Khadijetou',
                'telephone' => '+222 30 11 22 40',
                'adresse' => 'Hay Saken, Lot 156, Nouakchott',
                'date_naissance' => '2007-08-16',
                'genre' => 'F',
                'classe' => 'Terminale L',
                'has_account' => true,
                'email' => 'khadijetou.ebnou@student.ecole.com',
            ],
        ];
        
        foreach ($sampleStudents as $studentData) {
            $classe = $classes->where('nom_classe', $studentData['classe'])->first();
            if (!$classe) continue;

            // Avoid creating duplicate student records
            $existingEtudiant = Etudiant::where('nom', $studentData['nom'])
                ->where('prenom', $studentData['prenom'])
                ->where('id_classe', $classe->id_classe)
                ->first();

            if ($existingEtudiant) {
                $etudiant = $existingEtudiant;
            } else {
                // Create student
                $etudiant = Etudiant::create([
                    'nom' => $studentData['nom'],
                    'prenom' => $studentData['prenom'], 
                    'telephone' => $studentData['telephone'],
                    'adresse' => $studentData['adresse'],
                    'date_naissance' => $studentData['date_naissance'],
                    'genre' => $studentData['genre'],
                    'id_classe' => $classe->id_classe,
                    // matricule will be auto-generated
                ]);
            }

            // Create user account if specified and email not already used
            if ($studentData['has_account'] && isset($studentData['email'])) {
                if (!User::where('email', $studentData['email'])->exists()) {
                    $user = User::create([
                        'name' => trim($studentData['prenom'] . ' ' . $studentData['nom']),
                        'email' => $studentData['email'],
                        'password' => Hash::make('student123'),
                        'is_active' => true,
                        'email_verified_at' => now(),
                        'profile_type' => Etudiant::class,
                        'profile_id' => $etudiant->id_etudiant,
                    ]);
                    
                    $user->assignRole('student');
                } else {
                }
            }
        }
    }
    
    private function generateRandomStudents($classes): void
    {
        foreach ($classes as $classe) {
            // Generate 8-15 students per class
            $studentsCount = rand(8, 15);
            
            for ($i = 0; $i < $studentsCount; $i++) {
                $genre = rand(1, 2) == 1 ? 'M' : 'F';
                $nom = $this->getRandomMauritanianLastName();
                $prenom = $this->getRandomMauritanianFirstName($genre);
                $birthDate = $this->calculateBirthDate($classe->niveau);
                
                // Only create accounts for older students (niveau >= 12)
                $hasAccount = $classe->niveau >= 12 && rand(1, 100) <= 30; // 30% chance
                
                // Avoid creating duplicate student entries by name + class
                if (Etudiant::where('nom', $nom)->where('prenom', $prenom)->where('id_classe', $classe->id_classe)->exists()) {
                    continue;
                }

                $etudiant = Etudiant::create([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'telephone' => $this->generatePhoneNumber(),
                    'adresse' => $this->getRandomAddress(),
                    'date_naissance' => $birthDate,
                    'genre' => $genre,
                    'id_classe' => $classe->id_classe,
                ]);
                
                // Create user account if needed
                if ($hasAccount) {
                    $email = strtolower($prenom) . '.' . strtolower(str_replace(' ', '', $nom)) . '@student.ecole.com';
                    $email = $this->ensureUniqueEmail($email);
                    
                    $user = User::create([
                        'name' => trim($prenom . ' ' . $nom),
                        'email' => $email,
                        'password' => Hash::make('student123'),
                        'is_active' => rand(1, 100) <= 90, // 90% active
                        'email_verified_at' => now(),
                        'profile_type' => Etudiant::class,
                        'profile_id' => $etudiant->id_etudiant,
                    ]);
                    
                    $user->assignRole('student');
                }
            }
        }
    }
    
    private function getRandomMauritanianFirstName(string $genre): string
    {
        $maleNames = [
            'Mohamed', 'Ahmed', 'Sidi', 'Oumar', 'Abdallahi', 'Mohamed Lemine', 'Salem', 'Mahmoud',
            'Amadou', 'Ousmane', 'Ibrahim', 'Youssef', 'Hassan', 'Moctar', 'Cheikh', 'Brahim',
            'Moustapha', 'Sid Ahmed', 'Mohamed Vall', 'Ely', 'Yahya', 'Isselmou'
        ];
        
        $femaleNames = [
            'Fatimata', 'Aissata', 'Mariem', 'Khadija', 'Aminetou', 'Aïcha', 'Khadijetou',
            'Maryam', 'Coumba', 'Zeynab', 'Habiba', 'Selma', 'Rokia', 'Safiatou',
            'Nana', 'Véronique', 'Maimouna', 'Hawwa'
        ];
        
        return $genre === 'M' ? $maleNames[array_rand($maleNames)] : $femaleNames[array_rand($femaleNames)];
    }
    
    private function getRandomMauritanianLastName(): string
    {
        $lastNames = [
            'Ould Ahmed', 'Mint Sidi', 'Ba', 'Sy', 'Kane', 'Diallo', 'Ould Baba', 'Mint Vall',
            'Ould Mohamed', 'Mint Ebnou', 'Ould Abdallahi', 'Mint Mohamedou', 'Touré', 'Sow',
            'Ould Salem', 'Mint Ahmed', 'Camara', 'Traoré', 'Ould Cheikh', 'Mint Moctar',
            'Ould Brahim', 'Mint Yahya', 'Ould Sid Ahmed', 'Mint Isselmou', 'Yall', 'Thiam'
        ];
        
        return $lastNames[array_rand($lastNames)];
    }
    
    private function getRandomAddress(): string
    {
        $neighborhoods = [
            'Tevragh Zeina', 'Ksar', 'El Mina', 'Sebkha', 'Arafat', 'Toujounine',
            'Dar Naim', 'Riad', 'Hay Saken', 'Medina', 'Cinquième', 'Sixième'
        ];
        
        $streets = [
            'Quartier %d', 'Secteur %d', 'Bloc %s', 'Ilot %s', 'Villa %d', 'Lot %d',
            'Rue %s', 'Avenue %s'
        ];
        
        $neighborhood = $neighborhoods[array_rand($neighborhoods)];
        $street = sprintf($streets[array_rand($streets)], 
            in_array('%s', [$streets[array_rand($streets)]]) ? chr(65 + rand(0, 10)) : rand(1, 500)
        );
        
        return $neighborhood . ', ' . $street . ', Nouakchott';
    }
    
    private function generatePhoneNumber(): string
    {
        $prefixes = ['30', '31', '32', '33', '34', '36', '37', '38', '39'];
        $prefix = $prefixes[array_rand($prefixes)];
        $number = sprintf('%02d %02d %02d', rand(10, 99), rand(10, 99), rand(10, 99));
        
        return '+222 ' . $prefix . ' ' . $number;
    }
    
    private function calculateBirthDate(int $niveau): string
    {
        // Calculate appropriate age based on grade level
        $baseAge = $niveau + 2; // Approximate age formula
        $currentYear = date('Y');
        $birthYear = $currentYear - $baseAge + rand(-1, 1); // Add some variation
        
        $month = rand(1, 12);
        $day = rand(1, 28);
        
        return sprintf('%d-%02d-%02d', $birthYear, $month, $day);
    }
    
    private function ensureUniqueEmail(string $email): string
    {
        $originalEmail = $email;
        $counter = 1;
        
        while (User::where('email', $email)->exists()) {
            $email = str_replace('@', $counter . '@', $originalEmail);
            $counter++;
        }
        
        return $email;
    }
}
