<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationResource\Pages;
use App\Filament\Resources\EvaluationResource\RelationManagers;
use App\Models\Evaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Concerns\HasRoleBasedAccess;

class EvaluationResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = Evaluation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('app.gestion_academique');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.evaluations');
    }

    public static function getPluralLabel(): string
    {
        return __('app.evaluations');
    }

    public static function getModelLabel(): string
    {
        return __('app.evaluation');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('evaluation.view');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('evaluation.create');
    }

    public static function canEdit(Model $record): bool
    {
        // Can't edit if evaluation has ended or has grades
        if ($record->isLocked()) {
            return false;
        }
        
        return auth()->user()->hasPermissionTo('evaluation.edit');
    }

    public static function canDelete(Model $record): bool
    {
        // Can't delete if evaluation has any grades entered
        if ($record->hasGrades()) {
            return false;
        }
        
        return auth()->user()->hasPermissionTo('evaluation.delete');
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyRoleBasedTableScope(parent::getEloquentQuery(), [
            'classColumn' => 'evaluations.id_classe',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.evaluation_details'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('evaluation.create') || auth()->user()->hasPermissionTo('evaluation.edit'))
                    ->schema([
                        Forms\Components\TextInput::make('titre')
                            ->label(__('app.title'))
                            ->maxLength(191)
                            ->placeholder(__('app.eval_title_placeholder')),
                            
                        Forms\Components\Select::make('type')
                            ->label(__('app.type'))
                            ->required()
                            ->options([
                                'devoir' => 'Homework',
                                'interrogation' => 'Quiz',
                                'examen' => 'Exam',
                                'controle' => 'Test',
                                'projet' => 'Project',
                            ]),
                        
                        Forms\Components\Select::make('id_classe')
                            ->label(__('app.class'))
                            ->relationship('classe', 'nom_classe', function (Builder $query) {
                                return static::applyRoleBasedRelationScope($query, [
                                    'classColumn' => 'id_classe'
                                ]);
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($set) {
                                // Reset subject when class changes
                                $set('id_matiere', null);
                            }),
                            
                        Forms\Components\Select::make('id_matiere')
                            ->label(__('app.subject'))
                            ->options(function (callable $get) {
                                $classeId = $get('id_classe');
                                
                                if (!$classeId) {
                                    return [];
                                }
                                
                                $user = auth()->user();
                                
                                // Get subjects taught in the selected class
                                $query = \App\Models\Matiere::whereHas('classes', function ($q) use ($classeId) {
                                    $q->where('classes.id_classe', $classeId);
                                });
                                
                                // If user is a teacher, only show subjects they teach in this class
                                if ($user->hasRole(['teacher', 'enseignant']) && $user->profile) {
                                    $enseignant = $user->profile;
                                    $query->whereHas('enseignants', function ($q) use ($enseignant, $classeId) {
                                        $q->where('enseignants.id_enseignant', $enseignant->id_enseignant)
                                          ->where('enseignant_matiere_classe.id_classe', $classeId)
                                          ->where('enseignant_matiere_classe.active', true);
                                    });
                                }
                                
                                return $query->pluck('nom_matiere', 'id_matiere');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn (callable $get) => !$get('id_classe'))
                            ->helperText(fn (callable $get) => !$get('id_classe') 
                                ? __('app.selectionner_classe_dabord') 
                                : null),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make(__('app.grading'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('evaluation.create') || auth()->user()->hasPermissionTo('evaluation.edit'))
                    ->schema([
                        Forms\Components\TextInput::make('note_max')
                            ->label(__('app.note_maximum'))
                            ->required()
                            ->numeric()
                            ->default(20.00)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix(__('app.point')),
                            
                        Forms\Components\DatePicker::make('date')
                            ->label(__('app.date_evaluation'))
                            ->required()
                            ->displayFormat('d/m/Y'),
                    ])
                    ->columns(2),
                    
                // Read-only evaluation view for users with view-only permissions
                Forms\Components\Section::make(__('app.evaluation_consultation'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('evaluation.view') && !auth()->user()->hasPermissionTo('evaluation.create') && !auth()->user()->hasPermissionTo('evaluation.edit'))
                    ->schema([
                        Forms\Components\Placeholder::make('titre_display')
                            ->label(__('app.titre'))
                            ->content(fn ($record) => $record->titre ?: new \Illuminate\Support\HtmlString('<em class="text-gray-500">Sans titre</em>')),
                            
                        Forms\Components\Placeholder::make('type_display')
                            ->label(__('app.type'))
                            ->content(fn ($record) => new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">' . ucfirst($record->type) . '</span>')),
                            
                        Forms\Components\Placeholder::make('matiere_display')
                            ->label(__('app.matiere'))
                            ->content(fn ($record) => $record->matiere?->nom_matiere ?: '-'),
                            
                        Forms\Components\Placeholder::make('classe_display')
                            ->label(__('app.classe'))
                            ->content(fn ($record) => $record->classe?->nom_classe ?: '-'),
                            
                        Forms\Components\Placeholder::make('note_max_display')
                            ->label(__('app.note_maximum'))
                            ->content(fn ($record) => new \Illuminate\Support\HtmlString('<span class="text-lg font-semibold text-green-600">' . $record->note_max . '</span> points')),
                            
                        Forms\Components\Placeholder::make('date_display')
                            ->label(__('app.date_evaluation'))
                            ->content(fn ($record) => $record->date ? $record->date->format('d/m/Y') : '-'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titre')
                    ->label(__('app.evaluation'))
                    ->searchable(),
                    
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
                    
                Tables\Columns\TextColumn::make('matiere.code_matiere')
                    ->label(__('app.matiere'))
                    ->formatStateUsing(fn ($record) => __("app." . $record->matiere->code_matiere))
                    ->searchable()
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('classe.nom_classe')
                    ->label(__('app.classe'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('date')
                    ->label(__('app.date_evaluation'))
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('note_max')
                    ->label(__('app.note_maximum'))
                    ->sortable()
                    ->suffix(__('app.point')),
                    
                Tables\Columns\TextColumn::make('notes_count')
                    ->label(__('app.etudiants_notee'))
                    ->counts('notes')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\IconColumn::make('status')
                    ->label(__('app.status'))
                    ->state(fn ($record) => $record->isLocked())
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->tooltip(fn ($record) => $record->isLocked() 
                        ? __('app.evaluation_verrouille') 
                        : __('app.evaluation_modifiable')),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.cree_a'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('app.mis_a_jour_le'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('app.type'))
                    ->options([
                        'devoir' => __('app.homework'),
                        'interrogation' => __('app.quiz'),
                        'examen' => __('app.exam'),
                        'controle' => __('app.test'),
                        'projet' => __('app.project'),
                    ]),
                    
                Tables\Filters\SelectFilter::make('id_matiere')
                    ->label(__('app.matiere'))
                    ->relationship('matiere', 'nom_matiere')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('id_classe')
                    ->label(__('app.classe'))
                    ->relationship('classe', 'nom_classe')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('manage_grades')
                    ->label(__('app.saisir_notes'))
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->url(fn ($record) => static::getUrl('grades', ['record' => $record]))
                    ->visible(fn () => auth()->user()->can('grade.manage') || auth()->user()->hasRole(['super_admin', 'admin', 'teacher', 'enseignant'])),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
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
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'edit' => Pages\EditEvaluation::route('/{record}/edit'),
            'view' => Pages\ViewEvaluation::route('/{record}'),
            'grades' => Pages\ManageGrades::route('/{record}/grades'),
        ];
    }
}
