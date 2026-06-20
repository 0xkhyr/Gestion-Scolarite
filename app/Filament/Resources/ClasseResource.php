<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Support\Academic;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ClasseResource\Pages\ListClasses;
use App\Filament\Resources\ClasseResource\Pages\CreateClasse;
use App\Filament\Resources\ClasseResource\Pages\EditClasse;
use App\Filament\Resources\ClasseResource\Pages\ViewClasse;
use App\Filament\Resources\ClasseResource\Pages\ViewClasseTimetable;
use App\Filament\Resources\ClasseResource\Pages;
use App\Filament\Resources\ClasseResource\RelationManagers;
use App\Models\Classe;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Concerns\HasRoleBasedAccess;

class ClasseResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = Classe::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('app.gestion_academique');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.classes');
    }

    public static function getPluralLabel(): string
    {
        return __('app.classes');
    }

    public static function getModelLabel(): string
    {
        return __('app.classe');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('class.view');
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('class.view');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('class.create');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('class.edit');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('class.delete');
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyRoleBasedTableScope(parent::getEloquentQuery(), [
            'classColumn' => 'id_classe',
            'studentScope' => false,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.informations_classe'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('class.create') || auth()->user()->hasPermissionTo('class.edit'))
                    ->schema([
                        Select::make('niveau')
                            ->label(__('app.niveau'))
                            ->required()
                            ->options(Academic::levelOptionsGrouped())
                            ->searchable()
                            ->live()
                            // Clear the série whenever the level no longer supports one.
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (! Academic::levelHasSeries($state)) {
                                    $set('serie', null);
                                }
                            }),

                        Select::make('serie')
                            ->label(__('app.serie'))
                            ->options(Academic::serieOptions())
                            ->placeholder(__('app.serie_placeholder'))
                            // Séries exist only at lycée (5AS–7AS).
                            ->visible(fn (Get $get) => Academic::levelHasSeries($get('niveau')))
                            ->required(fn (Get $get) => Academic::levelHasSeries($get('niveau'))),

                        TextInput::make('groupe')
                            ->label(__('app.groupe'))
                            ->helperText(__('app.groupe_hint'))
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Read-only class view for users with view-only permissions
                Section::make(__('app.class_consultation'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('class.view') && !auth()->user()->hasPermissionTo('class.create') && !auth()->user()->hasPermissionTo('class.edit'))
                    ->schema([
                        Placeholder::make('nom_classe_display')
                            ->label(__('app.nom_classe'))
                            ->content(fn ($record) => new HtmlString('<span class="text-lg font-semibold text-blue-600">' . e($record->label) . '</span>')),
                            
                        Placeholder::make('niveau_display')
                            ->label(__('app.niveau'))
                            ->content(fn ($record) => new HtmlString('<span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-700/10">📚 ' . e(Academic::levelLabel($record->niveau)) . '</span>')),

                        Placeholder::make('serie_display')
                            ->label(__('app.serie'))
                            ->visible(fn ($record) => filled($record?->serie))
                            ->content(fn ($record) => new HtmlString('<span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-700/10">' . e($record->serie) . '</span>')),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('app.informations_classe'))
                ->icon('heroicon-o-academic-cap')
                ->columns(3)
                ->schema([
                    TextEntry::make('code')
                        ->label(__('app.code'))
                        ->state(fn (Classe $record) => $record->code)
                        ->badge()
                        ->color('gray'),

                    TextEntry::make('label')
                        ->label(__('app.nom_classe'))
                        ->state(fn (Classe $record) => $record->label)
                        ->weight(FontWeight::Bold),

                    TextEntry::make('cycle')
                        ->label(__('app.cycle'))
                        ->state(fn (Classe $record) => $record->cycle ? __('app.cycle_' . $record->cycle) : '—')
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('niveau')
                        ->label(__('app.niveau'))
                        ->formatStateUsing(fn ($state) => Academic::levelLabel($state))
                        ->badge()
                        ->color('info'),

                    TextEntry::make('serie')
                        ->label(__('app.serie'))
                        ->placeholder('—')
                        ->badge()
                        ->color('warning'),

                    TextEntry::make('groupe')
                        ->label(__('app.groupe'))
                        ->badge()
                        ->color('gray'),
                ])
                ->columnSpanFull(),

            Section::make(__('app.statistiques'))
                ->icon('heroicon-o-chart-bar')
                ->columns(3)
                ->schema([
                    TextEntry::make('etudiants_count')
                        ->label(__('app.etudiants'))
                        ->state(fn (Classe $record) => $record->etudiants()->count())
                        ->badge()
                        ->color('success'),

                    TextEntry::make('cours_count')
                        ->label(__('app.cours'))
                        ->state(fn (Classe $record) => $record->cours()->count())
                        ->badge()
                        ->color('warning'),

                    TextEntry::make('evaluations_count')
                        ->label(__('app.evaluations'))
                        ->state(fn (Classe $record) => $record->evaluations()->count())
                        ->badge()
                        ->color('danger'),
                ])
                ->columnSpanFull(),

            Section::make(__('app.matieres'))
                ->icon('heroicon-o-book-open')
                ->schema([
                    TextEntry::make('matieres')
                        ->hiddenLabel()
                        ->state(fn (Classe $record) => DB::table('enseignant_matiere_classe')
                            ->join('matieres', 'enseignant_matiere_classe.id_matiere', '=', 'matieres.id_matiere')
                            ->where('enseignant_matiere_classe.id_classe', $record->id_classe)
                            ->where('enseignant_matiere_classe.active', true)
                            ->distinct()
                            ->orderBy('matieres.code_matiere')
                            ->pluck('matieres.code_matiere')
                            ->map(fn ($code) => __('app.' . $code))
                            ->all())
                        ->badge()
                        ->color('primary')
                        ->placeholder('—'),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('app.code'))
                    ->state(fn ($record) => $record->code)
                    ->badge()
                    ->color('gray'),

                TextColumn::make('nom_classe')
                    ->label(__('app.nom_classe'))
                    ->formatStateUsing(fn ($record) => $record->label)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('niveau')
                    ->label(__('app.niveau'))
                    ->formatStateUsing(fn ($state) => Academic::levelLabel($state))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('serie')
                    ->label(__('app.serie'))
                    ->placeholder('—')
                    ->badge()
                    ->color('warning')
                    ->toggleable(),

                TextColumn::make('etudiants_count')
                    ->label(__('app.etudiants'))
                    ->sortable()
                    ->counts('etudiants')
                    ->badge()
                    ->color('success'),
                    
                TextColumn::make('cours_count')
                    ->label(__('app.cours'))
                    ->counts('cours')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                    
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
                SelectFilter::make('niveau')
                    ->label(__('app.level'))
                    ->options(Academic::levelOptionsGrouped()),

                SelectFilter::make('serie')
                    ->label(__('app.serie'))
                    ->options(Academic::serieOptions()),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('view-timetable')
                    ->label(__('app.emploi_temps'))
                    ->icon('heroicon-o-calendar-days')
                    ->color('info')
                    ->url(fn (Classe $record): string => static::getUrl('view-timetable', ['record' => $record])),
                    
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('niveau', 'asc')
            ->defaultPaginationPageOption(setting('system.items_per_page', 25));
    }

    public static function getRelations(): array
    {
        return [
            // Relation managers will be added as needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClasses::route('/'),
            'create' => CreateClasse::route('/create'),
            'view' => ViewClasse::route('/{record}'),
            'edit' => EditClasse::route('/{record}/edit'),
            'view-timetable' => ViewClasseTimetable::route('/{record}/timetable')
        ];
    }
}
