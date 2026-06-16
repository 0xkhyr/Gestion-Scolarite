<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages;
use App\Filament\Resources\NoteResource\RelationManagers;
use App\Models\Evaluation;
use App\Models\Etudiant;
use App\Models\Note;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Filament\Concerns\HasRoleBasedAccess;

class NoteResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = Note::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    
    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('app.gestion_academique');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.notes');
    }

    public static function getPluralLabel(): string
    {
        return __('app.notes');
    }

    public static function getModelLabel(): string
    {
        return __('app.note');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('grade.view');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('grade.create');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('grade.edit');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('grade.delete');
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyRoleBasedTableScope(parent::getEloquentQuery(), [
            'classRelation' => 'etudiant',
        ]);
    }

    /**
     * Check if a teacher can access a specific note
     */
    private static function canTeacherAccessNote(Model $note): bool
    {
        $user = auth()->user();
        
        if (!$user->hasRole('enseignant')) {
            return false;
        }
        
        $enseignant = $user->profile;
        if (!$enseignant) {
            return false;
        }
        
        // Check if the note's student is in teacher's classes
        $teacherClasses = $enseignant->classes()->pluck('id_classe');
        return $teacherClasses->contains($note->etudiant->id_classe);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.note_information'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('grade.create') || auth()->user()->hasPermissionTo('grade.edit'))
                    ->schema([
                        Forms\Components\Select::make('id_etudiant')
                            ->label(__('app.etudiant'))
                            ->relationship('etudiant', 'matricule', function (Builder $query) {
                                return static::applyRoleBasedRelationScope($query, [
                                    'classColumn' => 'id_classe'
                                ]);
                            })
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nom} {$record->prenom} ({$record->matricule})")
                            ->required()
                            ->searchable(['matricule'])
                            ->preload()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    $set('id_classe', null);
                                    return;
                                }

                                $etudiant = Etudiant::find($state);

                                $set('id_classe', $etudiant?->id_classe);
                            }),
                            
                        Forms\Components\Select::make('id_evaluation')
                            ->label(__('app.evaluation'))
                            ->options(static fn (callable $get) => self::getEvaluationOptions(
                                $get('id_classe'),
                                $get('id_evaluation'),
                            ))
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->live()
                            ->preload()
                            ->rules(function (callable $get, $record) {
                                return [
                                    function ($attribute, $value, $fail) use ($get, $record) {
                                        if (! $value || ! $get('id_etudiant')) {
                                            return;
                                        }

                                        $exists = Note::where('id_etudiant', $get('id_etudiant'))
                                            ->where('id_evaluation', $value);

                                        if ($record) {
                                            // Note's primary key is id_note, not id.
                                            $exists->where($record->getKeyName(), '!=', $record->getKey());
                                        }

                                        if ($exists->exists()) {
                                            $fail(__('app.note_deja_enregistree'));
                                        }
                                    },
                                ];
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $evaluation = Evaluation::find($state);
                                    if ($evaluation) {
                                        $set('id_matiere', $evaluation->id_matiere);
                                        $set('id_classe', $evaluation->id_classe);
                                        $set('type', $evaluation->type);
                                    }
                                }
                            }),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make(__('app.note'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('grade.create') || auth()->user()->hasPermissionTo('grade.edit'))
                    ->schema([
                        Forms\Components\TextInput::make('note')
                            ->label(__('app.note'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(fn (callable $get) => self::getEvaluationNoteMax($get))
                            ->helperText(fn (callable $get) => __('app.note_max_est', [
                                'max' => self::getEvaluationNoteMax($get),
                            ]))
                            ->reactive()
                            ->live(onBlur: true)
                            ->rules(fn (callable $get) => [
                                'required',
                                'numeric',
                                'min:0',
                                'max:' . self::getEvaluationNoteMax($get),
                            ]),
                            
                        Forms\Components\Textarea::make('commentaire')
                            ->label(__('app.commentaire'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
                    
                // Read-only grade view for teachers with view-only permissions
                Forms\Components\Section::make(__('app.grade_consultation'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('grade.view') && !auth()->user()->hasPermissionTo('grade.create') && !auth()->user()->hasPermissionTo('grade.edit'))
                    ->schema([
                        Forms\Components\Placeholder::make('etudiant_info')
                            ->label(__('app.etudiant'))
                            ->content(fn ($record) => $record->etudiant 
                                ? new \Illuminate\Support\HtmlString('<div class="space-y-1"><div class="font-medium text-gray-900">' . $record->etudiant->nom . ' ' . $record->etudiant->prenom . '</div><div class="text-sm text-gray-500">Matricule: ' . $record->etudiant->matricule . '</div></div>')
                                : '-'),
                                
                        Forms\Components\Placeholder::make('evaluation_info')
                            ->label(__('app.evaluation'))
                            ->content(fn ($record) => $record->evaluation 
                                ? new \Illuminate\Support\HtmlString('<div class="space-y-1"><div class="font-medium text-gray-900">' . $record->evaluation->nom . '</div><div class="text-sm text-gray-500">' . ucfirst($record->evaluation->type) . ' - ' . $record->evaluation->matiere->nom . '</div></div>')
                                : '-'),
                                
                        Forms\Components\Placeholder::make('note_display')
                            ->label(__('app.note_obtenue'))
                            ->content(fn ($record) => new \Illuminate\Support\HtmlString('<div class="flex items-center space-x-2"><span class="text-2xl font-bold text-blue-600">' . $record->note . '</span><span class="text-gray-500">/ ' . ($record->evaluation?->note_max ?? 20) . '</span></div>')),
                            
                        Forms\Components\Placeholder::make('commentaire_display')
                            ->label(__('app.commentaire'))
                            ->content(fn ($record) => $record->commentaire 
                                ? new \Illuminate\Support\HtmlString('<div class="text-sm text-gray-700 bg-gray-50 p-3 rounded-md">' . nl2br(e($record->commentaire)) . '</div>')
                                : new \Illuminate\Support\HtmlString('<div class="text-sm text-gray-500 italic">Aucun commentaire</div>')),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make(__('app.auto_filled'))
                    ->schema([
                        Forms\Components\Hidden::make('id_matiere'),
                        Forms\Components\Hidden::make('id_classe'),
                        Forms\Components\Hidden::make('type'),
                    ])
                    ->hidden(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('etudiant.matricule')
                    ->label(__('app.matricule'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('etudiant.nom')
                    ->label(__('app.etudiant'))
                    ->formatStateUsing(fn ($record) => "{$record->etudiant->nom} {$record->etudiant->prenom}")
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('etudiant', function (Builder $query) use ($search) {
                            $query->where('nom', 'like', "%{$search}%")
                                ->orWhere('prenom', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('evaluation.titre')
                    ->label(__('app.evaluation'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => __('app.' . ($record->matiere->code_matiere)) ?? 'N/A'),
                    
                Tables\Columns\TextColumn::make('note')
                    ->label(__('app.note'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(function ($state, $record) {
                        $max = $record->evaluation->note_max ?? 20;
                        $percent = ($state / $max) * 100;
                        if ($percent >= 75) return 'success';
                        if ($percent >= 50) return 'warning';
                        return 'danger';
                    })
                    ->formatStateUsing(function ($state, $record) {
                        $max = $record->evaluation->note_max ?? 20;
                        $percent = number_format(($state / $max) * 100, 1);
                        return "{$state}/{$max}";
                    }),
                    
                Tables\Columns\TextColumn::make('type')
                    ->label(__('app.type'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'devoir' => __('app.devoir'),
                        'interrogation' => __('app.quiz'),
                        'examen' => __('app.examen'),
                        'controle' => __('app.controle'),
                        'projet' => __('app.project'),
                            default => $state ?? __('app.type'),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'examen' => 'danger',
                        'controle' => 'warning',
                        'interrogation' => 'info',
                        'devoir' => 'success',
                        'projet' => 'primary',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('classe.nom_classe')
                    ->label(__('app.classe'))
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.cree_a'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_etudiant')
                    ->label(__('app.etudiant'))
                    ->relationship('etudiant', 'matricule')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nom} {$record->prenom} ({$record->matricule})")
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('id_evaluation')
                    ->label(__('app.evaluation'))
                    ->relationship('evaluation', 'titre')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('id_classe')
                    ->label(__('app.classe'))
                    ->relationship('classe', 'nom_classe')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('app.type'))
                    ->options([
                        'devoir' => __('app.devoir'),
                        'interrogation' => __('app.quiz'),
                        'examen' => __('app.examen'),
                        'controle' => __('app.controle'),
                        'projet' => __('app.project'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(setting('system.items_per_page', 25));
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotes::route('/'),
            'create' => Pages\CreateNote::route('/create'),
            'edit' => Pages\EditNote::route('/{record}/edit'),
        ];
    }

    protected static function getEvaluationNoteMax(callable $get): int
    {
        return self::resolveEvaluationNoteMax($get('id_evaluation'));
    }

    protected static function getEvaluationOptions(?int $classeId, ?int $evaluationId = null): array
    {
        if (! $classeId && ! $evaluationId) {
            return [];
        }

        $query = Evaluation::with('matiere')
            ->orderBy('titre');

        if ($classeId) {
            $query->where('id_classe', $classeId);
        } elseif ($evaluationId) {
            $query->whereKey($evaluationId);
        }

        return $query->get()
            ->mapWithKeys(fn (Evaluation $evaluation) => [
                $evaluation->getKey() => "{$evaluation->titre} ({$evaluation->matiere->nom_matiere})",
            ])
            ->toArray();
    }

    protected static function resolveEvaluationNoteMax(?int $evaluationId): int
    {
        return Evaluation::find($evaluationId)?->note_max ?? 20;
    }

    public static function fillRequiredEvaluationReferences(array $data): array
    {
        if (empty($data['id_evaluation'])) {
            return $data;
        }

        $evaluation = Evaluation::find($data['id_evaluation']);

        if (! $evaluation) {
            return $data;
        }

        return [
            ...$data,
            'id_matiere' => $data['id_matiere'] ?? $evaluation->id_matiere,
            'id_classe' => $data['id_classe'] ?? $evaluation->id_classe,
            'type' => $data['type'] ?? $evaluation->type,
        ];
    }

    public static function ensureUniqueNoteCombination(array $data, ?int $ignoreId = null): void
    {
        if (empty($data['id_etudiant']) || empty($data['id_evaluation'])) {
            return;
        }

        $query = Note::query()
            ->where('id_etudiant', $data['id_etudiant'])
            ->where('id_evaluation', $data['id_evaluation']);

        if ($ignoreId) {
            $query->whereKeyNot($ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'id_evaluation' => __('app.note_deja_enregistree'),
            ]);
        }
    }
}
