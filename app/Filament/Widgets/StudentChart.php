<?php

namespace App\Filament\Widgets;

use App\Models\Etudiant;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StudentChart extends ChartWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin', 'director', 'academic_coordinator', 'teacher', 'secretary']);
    }

    // protected int | string | array $columnSpan = [
    //     'xl' => 0.5,
    //     'lg' => 0.5,

    // ];
    // protected ?string $maxWidth = '2xl';

    public function getHeading(): string
    {
        $user = auth()->user();
        if ($user->hasRole('teacher')) {
            return __('app.my_students_distribution');
        }
        return __('app.repartition_etudiants');
    }

    protected function getData(): array
    {
        $user = auth()->user();
        
        if ($user->hasRole('teacher')) {
            // Teachers see only their students
            $enseignant = $user->profile;
            if (!$enseignant) {
                return [
                    'datasets' => [],
                    'labels' => [],
                ];
            }

            $teacherClasses = $enseignant->classes()->pluck('classes.id_classe');
            $query = Etudiant::whereIn('id_classe', $teacherClasses);
        } else {
            // All administrative roles see all students
            $query = Etudiant::query();
        }
        
        $data = $query->select('genre', DB::raw('count(*) as total'))
            ->groupBy('genre')
            ->pluck('total', 'genre')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => __('app.etudiants'),
                    'data' => [
                        $data['M'] ?? 0,
                        $data['F'] ?? 0,
                    ],
                    'backgroundColor' => [
                        '#36A2EB', // Blue for Men
                        '#FF6384', // Pink for Women
                    ],
                ],
            ],
            'labels' => [
                __('app.M'),
                __('app.F'),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
