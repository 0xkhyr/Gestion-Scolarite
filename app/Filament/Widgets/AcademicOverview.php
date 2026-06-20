<?php

namespace App\Filament\Widgets;

use App\Models\Evaluation;
use App\Models\Note;
use App\Support\Academic;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AcademicOverview extends BaseWidget
{
    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin', 'director', 'academic_coordinator']);
    }

    protected function getStats(): array
    {
        $max = Academic::maxGrade();
        $passing = Academic::passingGrade();

        $total = Note::count();
        $average = $total ? round((float) Note::avg('note'), 2) : 0;
        $passed = Note::where('note', '>=', $passing)->count();
        $passRate = $total ? round($passed / $total * 100) : 0;

        return [
            Stat::make(__('app.average_grade'), $average . ' / ' . $max)
                ->description(__('app.school_average'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color($average >= $passing ? 'success' : 'danger'),

            Stat::make(__('app.pass_rate'), $passRate . ' %')
                ->description(__('app.notes_passing', ['passing' => $passing, 'max' => $max]))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($passRate >= 50 ? 'success' : 'warning'),

            Stat::make(__('app.total_evaluations'), Evaluation::count())
                ->description(__('app.graded_notes') . ': ' . number_format($total, 0, '.', ' '))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),
        ];
    }
}
