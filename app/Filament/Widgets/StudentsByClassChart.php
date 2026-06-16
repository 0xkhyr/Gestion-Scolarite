<?php

namespace App\Filament\Widgets;

use App\Models\Classe;
use Filament\Widgets\ChartWidget;

class StudentsByClassChart extends ChartWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin', 'director', 'academic_coordinator', 'teacher', 'secretary']);
    }

    public function getHeading(): string
    {
        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            return __('app.my_students_by_class');
        }
        return __('app.etudiants_par_classe');
    }

    protected function getData(): array
    {
        $user = auth()->user();
        
        if ($user->hasRole('super_admin')) {
            // Admins see all classes
            $classes = Classe::withCount('etudiants')->get();
        } else if ($user->hasRole('teacher')) {
            // Teachers see only their classes
            $enseignant = $user->profile;
            if (!$enseignant) {
                return [
                    'datasets' => [],
                    'labels' => [],
                ];
            }
            
            $classes = $enseignant->classes()->withCount('etudiants')->get();
        } else {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => __('app.etudiants'),
                    'data' => $classes->pluck('etudiants_count')->toArray(),
                    'backgroundColor' => '#FFCE56',
                    'borderColor' => '#FFCE56',
                ],
            ],
            'labels' => $classes->pluck('nom_classe')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
