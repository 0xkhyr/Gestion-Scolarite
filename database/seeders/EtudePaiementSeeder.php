<?php

namespace Database\Seeders;

use App\Models\EtudePaiement;
use App\Models\Etudiant;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EtudePaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all students
        $etudiants = Etudiant::all();
        
        if ($etudiants->isEmpty()) {
            $this->command->warn('No students found. Please run EtudiantSeeder first.');
            return;
        }

        $paymentTypes = [
            'scolarite' => [
                'montant_range' => [500, 2000],
                'frequency' => 0.9, // 90% of students pay tuition
            ],
            'inscription' => [
                'montant_range' => [100, 300],
                'frequency' => 0.8, // 80% of students pay registration
            ],
            'examen' => [
                'montant_range' => [50, 150],
                'frequency' => 0.7, // 70% of students pay exam fees
            ],
            'uniforme' => [
                'montant_range' => [75, 200],
                'frequency' => 0.6, // 60% of students buy uniforms
            ],
            'transport' => [
                'montant_range' => [200, 600],
                'frequency' => 0.4, // 40% of students use transport
            ],
            'cantine' => [
                'montant_range' => [150, 400],
                'frequency' => 0.5, // 50% of students use cafeteria
            ],
            'autre' => [
                'montant_range' => [25, 500],
                'frequency' => 0.2, // 20% have other fees
            ],
        ];

        $statuses = ['paye', 'non_paye', 'partiel'];
        $statusWeights = [0.6, 0.25, 0.15]; // Most payments are paid

        
        foreach ($etudiants as $etudiant) {
            // Generate 1-4 payment records per student
            $numPayments = rand(1, 4);
            
            for ($i = 0; $i < $numPayments; $i++) {
                // Select payment type based on frequency
                $selectedType = $this->selectWeightedPaymentType($paymentTypes);
                $typeConfig = $paymentTypes[$selectedType];
                
                // Generate payment date (within last 12 months)
                $paymentDate = Carbon::now()->subDays(rand(1, 365));
                
                // Generate amount within range
                $montant = rand($typeConfig['montant_range'][0], $typeConfig['montant_range'][1]);
                
                // Select status based on weights
                $statut = $this->selectWeightedStatus($statuses, $statusWeights);
                
                // Adjust partial payments
                if ($statut === 'partiel') {
                    $montant = $montant * 0.5; // Partial payment is 50% of full amount
                }

                EtudePaiement::create([
                    'id_etudiant' => $etudiant->id_etudiant,
                    'typepaye' => $selectedType,
                    'montant' => $montant,
                    'statut' => $statut,
                    'date_paiement' => $paymentDate,
                ]);
            }
        }

        $this->command->info(EtudePaiement::count() . ' student payment records seeded.');
    }

    private function selectWeightedPaymentType(array $paymentTypes): string
    {
        $rand = mt_rand() / mt_getrandmax();

        foreach ($paymentTypes as $type => $config) {
            if ($rand < $config['frequency']) {
                return $type;
            }
        }

        return 'scolarite';
    }

    private function selectWeightedStatus(array $statuses, array $weights): string
    {
        $rand = mt_rand() / mt_getrandmax();
        $cumulative = 0;

        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($rand < $cumulative) {
                return $statuses[$index];
            }
        }

        return $statuses[0];
    }
}