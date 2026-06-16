<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use App\Models\Evaluation;
use App\Models\Etudiant;
use App\Models\Note;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Grade Entry Interface for Evaluations
 * 
 * Provides a spreadsheet-style interface for bulk grade entry.
 * - Shows all students in the evaluation's class
 * - Inline editable scores and comments
 * - Auto-creates or updates Note records
 * - Validates scores against evaluation's max_score
 * - Respects teacher-class assignments (via HasRoleBasedAccess)
 */
class ManageGrades extends Page implements HasTable
{
    use InteractsWithTable;
    use InteractsWithRecord;

    protected static string $resource = EvaluationResource::class;

    protected static string $view = 'filament.resources.evaluation-resource.pages.manage-grades';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        // Authorization: Ensure user can grade.manage for this evaluation
        abort_unless($this->canAccessEvaluation(), 403);
    }

    /**
     * Authorization check for evaluation access
     * - Super admins and admins: access all evaluations
     * - Teachers: only evaluations for their assigned classes
     */
    protected function canAccessEvaluation(): bool
    {
        $user = auth()->user();

        // Permission-based access
        if (!$user->can('grade.manage') && !$user->hasRole(['super_admin', 'admin'])) {
            return false;
        }

        // Super admin and admin bypass class restrictions
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        // Teachers: verify they teach this class
        if ($user->hasRole(['teacher', 'enseignant'])) {
            $enseignant = $user->profile;
            if (!$enseignant) {
                return false;
            }

            $teacherClasses = $enseignant->classes()->pluck('classes.id_classe');
            return $teacherClasses->contains($this->record->id_classe);
        }

        return false;
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = auth()->user();
        
        // Must have permission or admin role
        return $user->can('grade.manage') || $user->hasRole(['super_admin', 'admin', 'teacher', 'enseignant']);
    }

    public function getTitle(): string
    {
        return __('app.grade_entry') . ' - ' . ($this->record->titre ?? __('app.evaluation'));
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }

    /**
     * Table showing all students in the evaluation's class
     * with inline editable grade fields
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Etudiant::query()
                    ->where('id_classe', $this->record->id_classe)
                    ->orderBy('nom')
                    ->orderBy('prenom')
            )
            ->columns([
                Tables\Columns\TextColumn::make('matricule')
                    ->label(__('app.matricule'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('app.etudiant'))
                    ->searchable(['nom', 'prenom'])
                    ->sortable()
                    ->formatStateUsing(fn ($record) => "{$record->nom} {$record->prenom}"),

                Tables\Columns\TextInputColumn::make('note')
                    ->label(__('app.note') . ' /' . $this->record->note_max)
                    ->state(function (Etudiant $record): ?float {
                        $note = Note::where('id_etudiant', $record->id_etudiant)
                            ->where('id_evaluation', $this->record->id_evaluation)
                            ->first();
                        return $note?->note;
                    })
                    ->rules([
                        'nullable',
                        'numeric',
                        'min:0',
                        'max:' . $this->record->note_max,
                    ])
                    ->placeholder('0')
                    ->extraAttributes(['class' => 'font-semibold'])
                    ->updateStateUsing(function (Etudiant $record, $state) {
                        $this->saveGrade($record, $state, null);
                        return $state;
                    }),

                Tables\Columns\TextInputColumn::make('commentaire')
                    ->label(__('app.commentaire'))
                    ->state(function (Etudiant $record): ?string {
                        $note = Note::where('id_etudiant', $record->id_etudiant)
                            ->where('id_evaluation', $this->record->id_evaluation)
                            ->first();
                        return $note?->commentaire;
                    })
                    ->placeholder(__('app.optionnel'))
                    ->updateStateUsing(function (Etudiant $record, $state) {
                        $this->saveGrade($record, null, $state);
                        return $state;
                    }),

                Tables\Columns\IconColumn::make('status')
                    ->label(__('app.status'))
                    ->state(function (Etudiant $record): bool {
                        return Note::where('id_etudiant', $record->id_etudiant)
                            ->where('id_evaluation', $this->record->id_evaluation)
                            ->exists();
                    })
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('graded')
                    ->label(__('app.note_saisie'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('notes', function ($q) {
                            $q->where('id_evaluation', $this->record->id_evaluation);
                        }),
                        false: fn (Builder $query) => $query->whereDoesntHave('notes', function ($q) {
                            $q->where('id_evaluation', $this->record->id_evaluation);
                        }),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('delete_grade')
                    ->label(__('app.supprimer_note'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Etudiant $record) => Note::where('id_etudiant', $record->id_etudiant)
                        ->where('id_evaluation', $this->record->id_evaluation)
                        ->exists())
                    ->action(function (Etudiant $record) {
                        Note::where('id_etudiant', $record->id_etudiant)
                            ->where('id_evaluation', $this->record->id_evaluation)
                            ->delete();

                        Notification::make()
                            ->title(__('app.note_supprimee'))
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('assign_default')
                    ->label(__('app.attribuer_note_par_defaut'))
                    ->icon('heroicon-o-pencil-square')
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\TextInput::make('default_note')
                            ->label(__('app.note_par_defaut'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue($this->record->note_max)
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        foreach ($records as $student) {
                            $this->saveGrade($student, $data['default_note'], null);
                        }

                        Notification::make()
                            ->title(__('app.notes_assignees', ['count' => $records->count()]))
                            ->success()
                            ->send();
                    }),

                Tables\Actions\BulkAction::make('delete_grades')
                    ->label(__('app.supprimer_notes'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $count = Note::where('id_evaluation', $this->record->id_evaluation)
                            ->whereIn('id_etudiant', $records->pluck('id_etudiant'))
                            ->delete();

                        Notification::make()
                            ->title(__('app.notes_supprimees', ['count' => $count]))
                            ->success()
                            ->send();
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('back')
                    ->label(__('app.retour'))
                    ->icon('heroicon-o-arrow-left')
                    ->url(EvaluationResource::getUrl('index')),

                Tables\Actions\Action::make('view_evaluation')
                    ->label(__('app.voir_evaluation'))
                    ->icon('heroicon-o-eye')
                    ->url(EvaluationResource::getUrl('view', ['record' => $this->record])),
            ])
            ->emptyStateHeading(__('app.aucun_etudiant'))
            ->emptyStateDescription(__('app.classe_sans_etudiants'))
            ->striped()
            ->defaultPaginationPageOption(50)
            ->paginated([25, 50, 100, 'all']);
    }

    /**
     * Save or update grade for a student
     * 
     * @param Etudiant $student The student
     * @param float|null $note The score (null = no update)
     * @param string|null $commentaire The comment (null = no update)
     */
    protected function saveGrade(Etudiant $student, ?float $note, ?string $commentaire): void
    {
        // Find or create the Note record
        $gradeRecord = Note::firstOrNew([
            'id_etudiant' => $student->id_etudiant,
            'id_evaluation' => $this->record->id_evaluation,
        ]);

        // Auto-fill related fields from evaluation
        if (!$gradeRecord->exists) {
            $gradeRecord->id_matiere = $this->record->id_matiere;
            $gradeRecord->id_classe = $this->record->id_classe;
            $gradeRecord->type = $this->record->type;
        }

        // Update only provided fields
        if ($note !== null) {
            $gradeRecord->note = $note;
        }

        if ($commentaire !== null) {
            $gradeRecord->commentaire = $commentaire ?: null;
        }

        $gradeRecord->save();
    }

    /**
     * Get evaluation summary for the info panel
     */
    public function getEvaluationData(): array
    {
        return [
            'titre' => $this->record->titre ?? __('app.sans_titre'),
            'type' => __('app.' . $this->record->type),
            'matiere' => $this->record->matiere ? __('app.' . $this->record->matiere->code_matiere) : 'N/A',
            'classe' => $this->record->classe?->nom_classe ?? 'N/A',
            'note_max' => $this->record->note_max,
            'date' => $this->record->date_evaluation?->format('d/m/Y') ?? 'N/A',
            'coefficient' => $this->record->coefficient ?? 1,
            'total_students' => Etudiant::where('id_classe', $this->record->id_classe)->count(),
            'graded_count' => Note::where('id_evaluation', $this->record->id_evaluation)->count(),
        ];
    }
}
