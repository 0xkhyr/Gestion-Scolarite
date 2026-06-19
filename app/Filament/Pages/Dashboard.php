<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
use App\Filament\Widgets\TeacherStatsOverview;
use App\Filament\Widgets\TeacherTodaySchedule;
use App\Filament\Widgets\TeacherUpcomingEvaluations;
use App\Filament\Widgets\TeacherRecentNotes;
use App\Filament\Widgets\TeacherStudentPerformance;
use App\Filament\Widgets\TeacherStudentsByClass;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\AcademicOverview;
use App\Filament\Widgets\FinanceOverview;
use App\Filament\Widgets\StudentsByClassChart;
use App\Filament\Widgets\DailyScheduleWidget;
use App\Filament\Widgets\StudentChart;
use App\Filament\Widgets\PaymentsChart;
use App\Filament\Widgets\ActivityTimeline;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Actions\Action;
use App\Models\Classe;
use App\Models\Cours;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Illuminate\Support\Facades\Blade;
use Filament\Facades\Filament;
use App\Filament\Concerns\HasRoleBasedAccess;

class Dashboard extends BaseDashboard
{

    use HasRoleBasedAccess;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        
        // Allow access for administrative roles and teachers
        return $user->hasRole(['super_admin', 'admin', 'director', 'academic_coordinator', 'teacher', 'secretary', 'accountant']);
    }
    public function getTitle(): string | Htmlable
    {
        return __('app.tableau_de_bord');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.tableau_de_bord');
    }

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        
        // Only admins and teachers should see dashboard actions
        if (!$user->hasRole(['super_admin', 'admin', 'director', 'academic_coordinator', 'teacher'])) {
            return [];
        }
        
        return [
            Action::make('printTimetable')
                ->label(__('app.emploi_temps'))
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->schema([
                    Select::make('id_classe')
                        ->label(__('app.classe'))
                        ->options(function () {
                            $user = auth()->user();

                            // Admins see all classes
                            if ($user->hasRole(['super_admin', 'admin', 'director'])) {
                                return Classe::orderBy('niveau')->orderBy('serie')->orderBy('groupe')
                                    ->get()->mapWithKeys(fn ($c) => [$c->id_classe => $c->code]);
                            }

                            // Teachers see only their classes
                            if ($user->hasRole('teacher')) {
                                $enseignant = $user->profile;
                                if ($enseignant) {
                                    return $enseignant->classes()->get()
                                        ->mapWithKeys(fn ($c) => [$c->id_classe => $c->code]);
                                }
                            }

                            return [];
                        })
                        ->required()
                        ->searchable()
                        ->live(),
                    Placeholder::make('preview')
                        ->label('')
                        ->content(function (Get $get) {
                            $classeId = $get('id_classe');
                            if (!$classeId) {
                                return null;
                            }

                            $classe = Classe::find($classeId);
                            $query = Cours::where('id_classe', $classeId)
                                ->with(['matiere', 'enseignant']);
                            
                            // Apply RBAC filtering
                            $user = auth()->user();
                            if (!($user->hasRole('super_admin') || 
                                  $user->hasPermissionTo('timetable.manage') || 
                                  $user->hasPermissionTo('class.manage'))) {
                                $enseignant = $user->profile;
                                if ($enseignant) {
                                    $query->where('id_enseignant', $enseignant->id_enseignant);
                                }
                            }
                            
                            $courses = $query->get();

                            return view('filament.pages.timetable-preview', [
                                'classe' => $classe,
                                'courses' => $courses,
                            ]);
                        }),
                ])
                ->modalWidth('7xl')
                ->modalHeading(__('app.emploi_temps'))
                ->modalSubmitActionLabel(__('app.export_pdf'))
                ->action(function (array $data) {
                    $classe = Classe::find($data['id_classe']);
                    $query = Cours::where('id_classe', $data['id_classe'])
                        ->with(['matiere', 'enseignant']);
                    
                    // Apply RBAC filtering
                    $user = auth()->user();
                    if (!($user->hasRole('super_admin') || 
                          $user->hasPermissionTo('timetable.manage') || 
                          $user->hasPermissionTo('class.manage'))) {
                        $enseignant = $user->profile;
                        if ($enseignant) {
                            $query->where('id_enseignant', $enseignant->id_enseignant);
                        }
                    }
                    
                    $courses = $query->get();

                    $pdf = Pdf::loadView('pdf.timetable', [
                        'classe' => $classe,
                        'courses' => $courses,
                    ])->setPaper('a4', 'portrait');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, "emploi_du_temps_{$classe->code}.pdf");
                }),
        ];
    }

    public function getWidgets(): array
    {
        $user = auth()->user();
        
        if ($user && $user->hasRole('teacher')) {
            // Teacher widgets
            return [
                TeacherStatsOverview::class,
                TeacherTodaySchedule::class,
                TeacherUpcomingEvaluations::class,
                TeacherRecentNotes::class,
                TeacherStudentPerformance::class,
                TeacherStudentsByClass::class,
            ];
        }
        
        if ($user && $user->hasRole('secretary')) {
            // Secretary widgets - focused on student administration
            return [
                StatsOverview::class,
                StudentChart::class,
                StudentsByClassChart::class,
                DailyScheduleWidget::class,
            ];
        }

        if ($user && $user->hasRole('accountant')) {
            // Accountant widgets - focused on financial data
            return [
                StatsOverview::class,
                FinanceOverview::class,
                PaymentsChart::class,
                StudentsByClassChart::class,
            ];
        }

        // Admin widgets (super_admin, admin, director, academic_coordinator)
        return [
            StatsOverview::class,
            AcademicOverview::class,
            FinanceOverview::class,
            StudentChart::class,
            StudentsByClassChart::class,
            PaymentsChart::class,
            DailyScheduleWidget::class,
            ActivityTimeline::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 2,
        ];
    }
}
