<?php

namespace App\Filament\Widgets;

use App\Models\Classe;
use Filament\Widgets\ChartWidget;

class StudentsByClassChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

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
        
        if ($user->hasRole('teacher')) {
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
            // All administrative roles see all classes
            $classes = Classe::withCount('etudiants')
                ->orderBy('niveau')->orderBy('serie')->orderBy('groupe')
                ->get();
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
            'labels' => $classes->map(fn ($c) => $c->code)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
