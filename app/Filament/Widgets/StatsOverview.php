<?php

namespace App\Filament\Widgets;

use App\Models\Classe;
use App\Models\Cours;
use App\Models\Enseignant;
use App\Models\Etudiant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin', 'director', 'academic_coordinator', 'teacher', 'secretary']);
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        // Teachers see only their own scope.
        if ($user->hasRole('teacher')) {
            $enseignant = $user->profile;
            if (! $enseignant) {
                return [];
            }

            $teacherClasses = $enseignant->classes()->pluck('classes.id_classe');

            return [
                Stat::make(__('app.mes_etudiants'), Etudiant::whereIn('id_classe', $teacherClasses)->count())
                    ->description(__('app.students_in_my_classes'))
                    ->descriptionIcon('heroicon-m-academic-cap')
                    ->color('success'),
                Stat::make(__('app.mes_classes'), $teacherClasses->count())
                    ->description(__('app.classes_taught'))
                    ->descriptionIcon('heroicon-m-home-modern')
                    ->color('info'),
                Stat::make(__('app.mes_cours'), Cours::where('id_enseignant', $enseignant->id_enseignant)->count())
                    ->description(__('app.scheduled_courses'))
                    ->descriptionIcon('heroicon-m-calendar-days')
                    ->color('warning'),
                Stat::make(__('app.my_subjects'), $enseignant->matieres()->count())
                    ->description(__('app.matieres_enseignees'))
                    ->descriptionIcon('heroicon-m-book-open')
                    ->color('primary'),
            ];
        }

        // All administrative roles see school-wide stats.
        return [
            Stat::make(__('app.total_etudiants'), Etudiant::count())
                ->description(__('app.etudiants'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),
            Stat::make(__('app.total_enseignants'), Enseignant::count())
                ->description(__('app.enseignants'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            Stat::make(__('app.total_classes'), Classe::count())
                ->description(__('app.classes'))
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('warning'),
            Stat::make(__('app.total_cours'), Cours::whereNull('date')->count())
                ->description(__('app.cours'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
        ];
    }
}
