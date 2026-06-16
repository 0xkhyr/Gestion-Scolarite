<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoursResource\Pages;
use App\Filament\Resources\CoursResource\RelationManagers;
use App\Models\Cours;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Concerns\HasRoleBasedAccess;

class CoursResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = Cours::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('app.gestion_academique');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.cours');
    }

    public static function getPluralLabel(): string
    {
        return __('app.cours');
    }

    public static function getModelLabel(): string
    {
        return __('app.cours');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('course.view');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('course.create');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('course.edit');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('course.delete');
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyRoleBasedTableScope(parent::getEloquentQuery(), [
            'classColumn' => 'cours.id_classe',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.course_details'))
                    ->schema([
                        Forms\Components\Select::make('id_matiere')
                            ->label(__('app.matiere'))
                            ->relationship('matiere', 'code_matiere')
                            ->getOptionLabelFromRecordUsing(fn ($record) => __("app.{$record->code_matiere}"))
                            ->required()
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('id_enseignant')
                            ->label(__('app.enseignant'))
                            ->relationship('enseignant', 'id_enseignant')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nom} {$record->prenom}")
                            ->required()
                            ->searchable(['id_enseignant'])
                            ->preload(),
                            
                        Forms\Components\Select::make('id_classe')
                            ->label(__('app.classe'))
                            ->relationship('classe', 'nom_classe')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make(__('app.horaire'))
                    ->schema([
                        Forms\Components\Select::make('jour')
                            ->label(__('app.jour_semaine'))
                            ->required()
                            ->options([
                                'lundi' => __('app.lundi'),
                                'mardi' => __('app.mardi'),
                                'mercredi' => __('app.mercredi'),
                                'jeudi' => __('app.jeudi'),
                                'vendredi' => __('app.vendredi'),
                                'samedi' => __('app.samedi'),
                            ]),
                            
                        Forms\Components\TextInput::make('date_debut')
                            ->label(__('app.heure_debut'))
                            ->required()
                            ->type('text')
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'data-timepicker' => 'true',
                                'data-time-format' => self::getTimeFormat(),
                            ])
                            ->rules(['required', 'date_format:' . self::getTimeFormat()]),

                        Forms\Components\TextInput::make('date_fin')
                            ->label(__('app.heure_fin'))
                            ->required()
                            ->type('text')
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'data-timepicker' => 'true',
                                'data-time-format' => self::getTimeFormat(),
                            ])
                            ->rules(['required', 'date_format:' . self::getTimeFormat(), 'after:date_debut']),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make(__('app.description'))
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label(__('app.description_cours'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function getTimeFormat(): string
    {
        return config('app.time_format', 'H:i');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matiere.code_matiere')
                    ->label(__('app.matiere'))
                    ->formatStateUsing(fn ($record) => __("app." . $record->matiere->code_matiere))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('enseignant.nom')
                    ->label(__('app.enseignant'))
                    ->formatStateUsing(fn ($record) => "{$record->enseignant->nom} {$record->enseignant->prenom}")
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('enseignant', function (Builder $query) use ($search) {
                            $query->where('nom', 'like', "%{$search}%")
                                ->orWhere('prenom', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('classe.nom_classe')
                    ->label(__('app.classe'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('jour')
                    ->label(__('app.jour_semaine'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? __("app.{$state}") : __('app.jour_semaine'))
                    ->color('warning'),
                    
                Tables\Columns\TextColumn::make('date_debut')
                    ->label(__('app.heure_debut'))
                    ->time(self::getTimeFormat()),

                Tables\Columns\TextColumn::make('date_fin')
                    ->label(__('app.heure_fin'))
                    ->time(self::getTimeFormat()),
                    
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
                Tables\Filters\SelectFilter::make('id_enseignant')
                    ->label(__('app.enseignant'))
                    ->relationship('enseignant', 'id_enseignant')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nom} {$record->prenom}")
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('id_classe')
                    ->label(__('app.classe'))
                    ->relationship('classe', 'nom_classe')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('jour')
                    ->label(__('app.jour_semaine'))
                    ->options([
                                'lundi' => __('app.lundi'),
                                'mardi' => __('app.mardi'),
                                'mercredi' => __('app.mercredi'),
                                'jeudi' => __('app.jeudi'),
                                'vendredi' => __('app.vendredi'),
                                'samedi' => __('app.samedi'),
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
            ->defaultSort('jour', 'asc');
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
            'index' => Pages\ListCours::route('/'),
            'create' => Pages\CreateCours::route('/create'),
            'edit' => Pages\EditCours::route('/{record}/edit'),
        ];
    }
}
