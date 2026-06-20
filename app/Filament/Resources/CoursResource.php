<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\CoursResource\Pages\ListCours;
use App\Filament\Resources\CoursResource\Pages\CreateCours;
use App\Filament\Resources\CoursResource\Pages\EditCours;
use App\Filament\Resources\CoursResource\Pages;
use App\Filament\Resources\CoursResource\RelationManagers;
use App\Models\Cours;
use Filament\Forms;
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

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';
    
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.course_details'))
                    ->schema([
                        Select::make('id_matiere')
                            ->label(__('app.matiere'))
                            ->relationship('matiere', 'code_matiere')
                            ->getOptionLabelFromRecordUsing(fn ($record) => __("app.{$record->code_matiere}"))
                            ->required()
                            ->searchable()
                            ->preload(),
                            
                        Select::make('id_enseignant')
                            ->label(__('app.enseignant'))
                            ->relationship('enseignant', 'id_enseignant')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nom} {$record->prenom}")
                            ->required()
                            ->searchable(['id_enseignant'])
                            ->preload(),
                            
                        Select::make('id_classe')
                            ->label(__('app.classe'))
                            ->relationship('classe', 'nom_classe')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->label)
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(3),
                    
                Section::make(__('app.horaire'))
                    ->schema([
                        Radio::make('seance_type')
                            ->label(__('app.seance_type'))
                            ->options([
                                'recurrent' => __('app.cours_recurrent'),
                                'ponctuel' => __('app.cours_ponctuel'),
                            ])
                            ->descriptions([
                                'recurrent' => __('app.cours_recurrent_hint'),
                                'ponctuel' => __('app.cours_ponctuel_hint'),
                            ])
                            ->default('recurrent')
                            ->live()
                            ->dehydrated(false)
                            ->afterStateHydrated(function (Set $set, ?Cours $record): void {
                                if ($record) {
                                    $set('seance_type', $record->date ? 'ponctuel' : 'recurrent');
                                }
                            })
                            ->inline()
                            ->columnSpanFull(),

                        Select::make('jour')
                            ->label(__('app.jour_semaine'))
                            ->options([
                                'lundi' => __('app.lundi'),
                                'mardi' => __('app.mardi'),
                                'mercredi' => __('app.mercredi'),
                                'jeudi' => __('app.jeudi'),
                                'vendredi' => __('app.vendredi'),
                                'samedi' => __('app.samedi'),
                            ])
                            ->visible(fn (Get $get): bool => $get('seance_type') !== 'ponctuel')
                            ->required(fn (Get $get): bool => $get('seance_type') !== 'ponctuel'),

                        DatePicker::make('date')
                            ->label(__('app.date'))
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('seance_type') === 'ponctuel')
                            ->required(fn (Get $get): bool => $get('seance_type') === 'ponctuel'),

                        TextInput::make('date_debut')
                            ->label(__('app.heure_debut'))
                            ->required()
                            ->type('text')
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'data-timepicker' => 'true',
                                'data-time-format' => self::getTimeFormat(),
                            ])
                            ->rules(['required', 'date_format:' . self::getTimeFormat()]),

                        TextInput::make('date_fin')
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
                    
                Section::make(__('app.description'))
                    ->schema([
                        Textarea::make('description')
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
                TextColumn::make('matiere.code_matiere')
                    ->label(__('app.matiere'))
                    ->formatStateUsing(fn ($record) => __("app." . $record->matiere->code_matiere))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                    
                TextColumn::make('enseignant.nom')
                    ->label(__('app.enseignant'))
                    ->formatStateUsing(fn ($record) => "{$record->enseignant->nom} {$record->enseignant->prenom}")
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('enseignant', function (Builder $query) use ($search) {
                            $query->where('nom', 'like', "%{$search}%")
                                ->orWhere('prenom', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),
                    
                TextColumn::make('classe.nom_classe')
                    ->label(__('app.classe'))
                    ->formatStateUsing(fn ($record) => $record->classe?->code)
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                TextColumn::make('jour')
                    ->label(__('app.jour_semaine'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? __("app.{$state}") : __('app.jour_semaine'))
                    ->color('warning'),

                TextColumn::make('date')
                    ->label(__('app.date'))
                    ->date()
                    ->placeholder('—')
                    ->badge()
                    ->color('danger')
                    ->sortable(),

                TextColumn::make('date_debut')
                    ->label(__('app.heure_debut'))
                    ->time(self::getTimeFormat()),

                TextColumn::make('date_fin')
                    ->label(__('app.heure_fin'))
                    ->time(self::getTimeFormat()),
                    
                TextColumn::make('created_at')
                    ->label(__('app.cree_a'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('app.mis_a_jour_le'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_enseignant')
                    ->label(__('app.enseignant'))
                    ->relationship('enseignant', 'id_enseignant')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nom} {$record->prenom}")
                    ->searchable()
                    ->preload(),
                    
                SelectFilter::make('id_classe')
                    ->label(__('app.classe'))
                    ->relationship('classe', 'nom_classe')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->label)
                    ->searchable()
                    ->preload(),
                    
                SelectFilter::make('jour')
                    ->label(__('app.jour_semaine'))
                    ->options([
                                'lundi' => __('app.lundi'),
                                'mardi' => __('app.mardi'),
                                'mercredi' => __('app.mercredi'),
                                'jeudi' => __('app.jeudi'),
                                'vendredi' => __('app.vendredi'),
                                'samedi' => __('app.samedi'),
                            ]),

                TernaryFilter::make('seance_type')
                    ->label(__('app.seance_type'))
                    ->placeholder(__('app.all'))
                    ->trueLabel(__('app.cours_ponctuel'))
                    ->falseLabel(__('app.cours_recurrent'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('date'),
                        false: fn (Builder $query) => $query->whereNull('date'),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListCours::route('/'),
            'create' => CreateCours::route('/create'),
            'edit' => EditCours::route('/{record}/edit'),
        ];
    }
}
