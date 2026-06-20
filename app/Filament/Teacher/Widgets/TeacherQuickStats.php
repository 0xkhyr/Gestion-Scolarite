<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Evaluation;
use App\Models\Etudiant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeacherQuickStats extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->hasRole('teacher') || auth()->user()->hasRole('enseignant');
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $enseignant = $user->profile;
        
        if (!$enseignant) {
            return [];
        }
        
        $teacherClasses = $enseignant->classes()->pluck('classes.id_classe');
        
        // Count students by gender in teacher's classes
        $maleStudents = Etudiant::whereIn('id_classe', $teacherClasses)
            ->where('genre', 'M')
            ->count();
            
        $femaleStudents = Etudiant::whereIn('id_classe', $teacherClasses)
            ->where('genre', 'F')
            ->count();
        
        // Count students who have notes (active students)
        $activeStudents = Etudiant::whereIn('id_classe', $teacherClasses)
            ->whereHas('notes')
            ->count();
            
        // Count total evaluations for teacher's classes
        $totalEvaluations = Evaluation::whereIn('id_classe', $teacherClasses)->count();
        
        return [
            Stat::make(__('app.etudiants_hommes'), $maleStudents)
                ->description(__('app.male_students'))
                ->descriptionIcon('heroicon-m-user')
                ->color('blue'),
                
            Stat::make(__('app.etudiantes_femmes'), $femaleStudents)
                ->description(__('app.female_students'))
                ->descriptionIcon('heroicon-m-user')
                ->color('pink'),
                
            Stat::make(__('app.active_students'), $activeStudents)
                ->description(__('app.with_grades'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),
                
            Stat::make(__('app.my_evaluations'), $totalEvaluations)
                ->description(__('app.evaluations_creees'))
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('warning'),
        ];
    }
}