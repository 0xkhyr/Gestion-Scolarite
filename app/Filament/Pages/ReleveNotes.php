<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Etudiant;
use App\Models\Note;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Barryvdh\DomPDF\Facade\Pdf;
use ArPHP\I18N\Arabic;
use App\Models\Classe;
use App\Filament\Concerns\HasRoleBasedAccess;

class ReleveNotes extends Page implements HasForms
{
    use InteractsWithForms, HasRoleBasedAccess;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    public static function getNavigationGroup(): ?string
    {
        return __('app.gestion_academique');
    }
    protected static string $view = 'filament.pages.releve-notes';

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('print')
                ->label(__('app.imprimer_releve'))
                ->icon('heroicon-o-printer')
                ->color('success')
                ->visible(fn () => $this->id_etudiant
                    && (auth()->user()?->hasRole('super_admin') || auth()->user()?->can('transcript.export')))
                ->action(fn () => $this->printReleve()),
        ];
    }

    public ?int $id_classe = null;
    public ?int $id_etudiant = null;
    public ?string $classCode = null;
    public ?string $etudiantMatricule = null;
    protected bool $skipIdEtudiantReset = false;

    protected array $queryString = [
        'classCode' => ['except' => null, 'as' => 'classe'],
        'etudiantMatricule' => ['except' => null, 'as' => 'etudiant'],
    ];

    public function mount(): void
    {
        $this->hydrateClassFromCode();
        $this->hydrateStudentFromMatricule();

        // Security check: ensure the teacher has access to the hydrated class/student
        if ($this->id_classe) {
            $classExists = static::applyRoleBasedRelationScope(Classe::where('id_classe', $this->id_classe), [
                'classColumn' => 'id_classe'
            ])->exists();
            
            if (!$classExists) {
                $this->id_classe = null;
                $this->classCode = null;
                $this->id_etudiant = null;
                $this->etudiantMatricule = null;
            }
        }

        if ($this->id_etudiant) {
            $studentExists = static::applyRoleBasedRelationScope(Etudiant::where('id_etudiant', $this->id_etudiant), [
                'classColumn' => 'id_classe'
            ])->exists();

            if (!$studentExists) {
                $this->id_etudiant = null;
                $this->etudiantMatricule = null;
            }
        }

        $this->form->fill([
            'id_classe' => $this->id_classe,
            'id_etudiant' => $this->id_etudiant,
        ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('app.releve_notes');
    }

    public function getTitle(): string
    {
        return __('app.releve_notes');
    }

    /**
     * Filament Pages gate access via canAccess() (not canViewAny()). This also
     * drives whether the page is reachable by URL.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('super_admin') || $user->can('transcript.view'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Grid::make(2)->schema([
                        Select::make('id_classe')
                            ->label(__('app.classe'))
                            ->options(function () {
                                return static::applyRoleBasedRelationScope(Classe::query(), [
                                    'classColumn' => 'id_classe'
                                ])->pluck('nom_classe', 'id_classe');
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($set) {
                                $set('id_etudiant', null);
                            }),

                        Select::make('id_etudiant')
                            ->label(__('app.rechercher_etudiant'))
                            ->placeholder(__('app.recherche_par_nom_ou_matricule'))
                            ->options(function ($get) {
                                $classeId = $get('id_classe');
                                $query = Etudiant::query();
                                
                                if ($classeId) {
                                    $query->where('id_classe', $classeId);
                                }
                                
                                // Apply role-based scoping
                                static::applyRoleBasedRelationScope($query, [
                                    'classColumn' => 'id_classe'
                                ]);
                                
                                return $query->get()->mapWithKeys(function ($etudiant) {
                                    return [$etudiant->id_etudiant => "{$etudiant->nom} {$etudiant->prenom} ({$etudiant->matricule})"];
                                });
                            })
                            ->searchable()
                            ->live()
                            ->required()
                            ->disabled(fn ($get) => !$get('id_classe') && Etudiant::count() > 50), // Disable if too many students and no class selected
                    ])
                ])
            ]);
    }

    public function updatedIdEtudiant($value)
    {
        if (!$value) {
            $this->etudiantMatricule = null;
            return;
        }
        $student = Etudiant::find($value);
        if ($student && $this->etudiantMatricule !== $student->matricule) {
            $this->etudiantMatricule = $student->matricule;
        }
        $this->form->fill([
            'id_classe' => $this->id_classe,
            'id_etudiant' => $this->id_etudiant,
        ]);
    }

    public function updatedIdClasse($value)
    {
        if (!$value) {
            $this->classCode = null;
            $this->id_etudiant = null;
            $this->etudiantMatricule = null;
            return;
        }

        $classe = Classe::find($value);
        if ($classe && $this->classCode !== $classe->nom_classe) {
            $this->classCode = $classe->nom_classe;
        }

        if ($this->skipIdEtudiantReset) {
            $this->skipIdEtudiantReset = false;
        } else {
            $this->id_etudiant = null;
            $this->etudiantMatricule = null;
        }
        $this->form->fill([
            'id_classe' => $this->id_classe,
            'id_etudiant' => null,
        ]);
    }

    public function updatedClassCode($value)
    {
        if (!$value) {
            $this->id_classe = null;
            return;
        }

        $classe = Classe::where('nom_classe', $value)->first();
        if ($classe && $classe->id_classe !== $this->id_classe) {
            $this->id_classe = $classe->id_classe;
        }
    }

    public function updatedEtudiantMatricule($value)
    {
        if (!$value) {
            $this->id_etudiant = null;
            return;
        }

        $student = Etudiant::where('matricule', $value)->first();
        if (!$student) {
            $this->id_etudiant = null;
            return;
        }

        if ($this->id_classe !== $student->id_classe) {
            $this->skipIdEtudiantReset = true;
            $this->id_classe = $student->id_classe;
        }

        if ($this->id_etudiant !== $student->id_etudiant) {
            $this->id_etudiant = $student->id_etudiant;
        }
    }

    public function getEtudiantProperty()
    {
        if (!$this->id_etudiant) return null;
        return Etudiant::with(['classe'])->find($this->id_etudiant);
    }

    public function getNotesProperty()
    {
        if (!$this->id_etudiant) return collect();
        
        $etudiant = $this->etudiant;
        if (!$etudiant || !$etudiant->id_classe) return collect();

        return $this->getStudentNotes($etudiant);
    }

    protected function getStudentNotes($etudiant)
    {
        // Récupérer toutes les évaluations de la classe de l'élève
        $evaluations = \App\Models\Evaluation::where('id_classe', $etudiant->id_classe)
            ->with('matiere')
            ->get();

        // Récupérer les notes existantes de l'élève
        $notesExistantes = Note::where('id_etudiant', $etudiant->id_etudiant)
            ->get()
            ->keyBy('id_evaluation');

        // Fusionner : pour chaque évaluation, on prend la note ou on met 0
        return $evaluations->map(function ($evaluation) use ($notesExistantes) {
            $note = $notesExistantes->get($evaluation->id_evaluation);
            
            return (object) [
                'evaluation' => $evaluation,
                'note' => $note ? $note->note : 0,
                'commentaire' => $note ? $note->commentaire : __('app.absent_ou_non_note'),
                'is_absence' => !$note,
            ];
        });
    }

    public function getClassRankingProperty()
    {
        if (!$this->id_classe) return collect();

        $query = Etudiant::where('id_classe', $this->id_classe);
        
        // Ensure teacher can only see students in their assigned classes
        static::applyRoleBasedRelationScope($query, [
            'classColumn' => 'id_classe'
        ]);

        $students = $query->get();

        return $students->map(function ($student) {
            $notes = $this->getStudentNotes($student);
            $moyenne = $notes->isEmpty() ? 0 : $notes->avg('note');
            
            return (object) [
                'id_etudiant' => $student->id_etudiant,
                'nom_complet' => "{$student->nom} {$student->prenom}",
                'matricule' => $student->matricule,
                'moyenne' => $moyenne,
                'mention' => $this->getMention($moyenne),
            ];
        })->sortByDesc('moyenne')->values();
    }

    public function getMoyenneProperty()
    {
        $notes = $this->getNotesProperty();
        if ($notes->isEmpty()) return 0;
        return $notes->avg('note');
    }

    public function getMention($moyenne)
    {
        if ($moyenne >= 16) return __('app.tres_bien');
        if ($moyenne >= 14) return __('app.bien');
        if ($moyenne >= 12) return __('app.assez_bien');
        if ($moyenne >= 10) return __('app.passable');
        return __('app.insuffisant');
    }

    public function selectStudent($id)
    {
        $this->id_etudiant = $id;
        // Forces the form to sync with the new state
        $this->form->fill([
            'id_classe' => $this->id_classe,
            'id_etudiant' => $id,
        ]);
        $student = Etudiant::with('classe')->find($id);
        if ($student) {
            $nomClasse = $student->classe->nom_classe ?? null;
            if ($nomClasse && $this->classCode !== $nomClasse) {
                $this->classCode = $nomClasse;
            }
            if ($this->etudiantMatricule !== $student->matricule) {
                $this->etudiantMatricule = $student->matricule;
            }
        }
    }

    protected function hydrateClassFromCode(): void
    {
        if (!$this->classCode) {
            return;
        }

        $classe = Classe::where('nom_classe', $this->classCode)->first();
        if ($classe) {
            $this->id_classe = $classe->id_classe;
        }
    }

    protected function hydrateStudentFromMatricule(): void
    {
        if (!$this->etudiantMatricule) {
            return;
        }

        $student = Etudiant::where('matricule', $this->etudiantMatricule)->first();
        if (!$student) {
            return;
        }

        if ($this->id_classe !== $student->id_classe) {
            $this->skipIdEtudiantReset = true;
            $this->id_classe = $student->id_classe;
        }
        $this->id_etudiant = $student->id_etudiant;
    }

    public function printReleve()
    {
        // PDF export requires the transcript.export permission (super_admin exempt).
        abort_unless(
            auth()->user()?->hasRole('super_admin') || auth()->user()?->can('transcript.export'),
            403
        );

        $etudiant = $this->etudiant;
        if (!$etudiant) return;

        $notes = $this->notes;
        $moyenne = $this->moyenne;
        $mention = $this->getMention($moyenne);
        $isRtl = app()->getLocale() == 'ar';
        $ar = new Arabic();

        $pdf = Pdf::loadView('pdf.releve_notes', [
            'etudiant' => $etudiant,
            'notes' => $notes,
            'moyenne' => $moyenne,
            'mention' => $mention,
            'isRtl' => $isRtl,
            'ar' => $ar,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Releve_Notes_' . $etudiant->matricule . '.pdf');
    }
}
