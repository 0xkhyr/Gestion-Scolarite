<?php

namespace App\Filament\Widgets;

use App\Models\Etudiant;
use Filament\Widgets\ChartWidget;

class TeacherStudentsByClass extends ChartWidget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'half';

    public static function canView(): bool
    {
        return auth()->user()->hasRole('teacher');
    }

    protected ?string $heading = 'Étudiants par classe';

    protected function getData(): array
    {
        $user = auth()->user();
        $enseignant = $user->profile;
        
        if (!$enseignant) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }
        
        // Get teacher's classes with student counts
        $classesData = $enseignant->classes()
            ->withCount('etudiants')
            ->get()
            ->pluck('etudiants_count', 'nom_classe');
        
        return [
            'datasets' => [
                [
                    'label' => 'Nombre d\'étudiants',
                    'data' => $classesData->values()->toArray(),
                    'backgroundColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)',
                    ],
                ],
            ],
            'labels' => $classesData->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}