<?php

namespace App\Filament\Widgets;

use App\Models\Etudiant;
use App\Models\Note;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TeacherStudentPerformance extends ChartWidget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'half';

    public static function canView(): bool
    {
        return auth()->user()->hasRole('teacher');
    }

    public function getHeading(): string
    {
        return __('app.my_students_performance');
    }

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
        
        // Get teacher's classes
        $teacherClasses = $enseignant->classes()->pluck('classes.id_classe');
        
        // Get performance distribution (excellent, good, average, poor)
        $notes = Note::whereHas('etudiant', function ($query) use ($teacherClasses) {
            $query->whereIn('id_classe', $teacherClasses);
        })
        ->with('evaluation')
        ->get();
        
        $performance = [
            'excellent' => 0, // >= 16/20 (80%)
            'good' => 0,      // >= 12/20 (60%)
            'average' => 0,   // >= 10/20 (50%)
            'poor' => 0,      // < 10/20 (50%)
        ];
        
        foreach ($notes as $note) {
            $max = $note->evaluation->note_max ?? 20;
            $percentage = ($note->note / $max) * 100;
            
            if ($percentage >= 80) {
                $performance['excellent']++;
            } elseif ($percentage >= 60) {
                $performance['good']++;
            } elseif ($percentage >= 50) {
                $performance['average']++;
            } else {
                $performance['poor']++;
            }
        }
        
        return [
            'datasets' => [
                [
                    'label' => __('app.notes'),
                    'data' => array_values($performance),
                    'backgroundColor' => [
                        '#10B981', // Green for excellent
                        '#3B82F6', // Blue for good  
                        '#F59E0B', // Yellow for average
                        '#EF4444', // Red for poor
                    ],
                ],
            ],
            'labels' => [
                __('app.excellent') . ' (≥80%)',
                __('app.bien') . ' (≥60%)',
                __('app.medium') . ' (≥50%)',
                __('app.low') . ' (<50%)',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}