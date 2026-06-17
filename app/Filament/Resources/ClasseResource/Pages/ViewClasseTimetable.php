<?php

namespace App\Filament\Resources\ClasseResource\Pages;

use App\Filament\Resources\ClasseResource;
use App\Models\Classe;
use App\Models\Cours;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Filament\Concerns\HasRoleBasedAccess;

class ViewClasseTimetable extends ViewRecord
{
    use HasRoleBasedAccess;

    protected static string $resource = ClasseResource::class;

    protected static string $view = 'filament.resources.classe-resource.pages.view-classe-timetable';

    public function getTitle(): string
    {
        return __('app.emploi_temps') . ' - ' . $this->record->nom_classe;
    }

    public static function getNavigationLabel(): string
    {
        return __('app.emploi_temps');
    }

    /**
     * Check if user can view full timetable (not just their own courses)
     */
    protected function canViewFullTimetable(): bool
    {
        $user = auth()->user();
        
        return $user->hasRole('super_admin') || 
               $user->hasPermissionTo('timetable.manage') || 
               $user->hasPermissionTo('class.manage');
    }

    /**
     * Get courses with RBAC filtering
     */
    protected function getCourses(): \Illuminate\Database\Eloquent\Collection
    {
        $query = Cours::where('id_classe', $this->record->id_classe)
            ->with(['matiere', 'enseignant']);
        
        // Filter by teacher if they can't view full timetable
        if (!$this->canViewFullTimetable()) {
            $enseignant = auth()->user()->profile;
            if ($enseignant) {
                $query->where('id_enseignant', $enseignant->id_enseignant);
            }
        }
        
        return $query->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make()
                ->url(fn () => ClasseResource::getUrl('edit', ['record' => $this->record])),
            Action::make('exportPdf')
                ->label(__('app.export_pdf'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $pdf = Pdf::loadView('pdf.timetable', [
                        'classe' => $this->record,
                        'courses' => $this->getCourses(),
                    ])->setPaper('a4', 'portrait');

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        "emploi_du_temps_{$this->record->nom_classe}.pdf"
                    );
                }),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'classe' => $this->record,
            'courses' => $this->getCourses(),
        ];
    }

    public static function canView($record = null): bool
    {
        $user = auth()->user();
        
        if (!$user || !$user->hasPermissionTo('timetable.view')) {
            return false;
        }
        
        // Teachers can only view timetables for their assigned classes
        if ($record && $user->hasRole(['teacher', 'enseignant'])) {
            return static::canTeacherAccessRecord($record);
        }
        
        return true;
    }
}