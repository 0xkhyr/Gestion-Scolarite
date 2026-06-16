<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClasseResource\Pages;
use App\Filament\Resources\ClasseResource\RelationManagers;
use App\Models\Classe;
use Filament\Forms;
use Filament\Forms\Form;
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

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.informations_classe'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('class.create') || auth()->user()->hasPermissionTo('class.edit'))
                    ->schema([
                        Forms\Components\TextInput::make('nom_classe')
                            ->label(__('app.nom_classe'))
                            ->required()
                            ->maxLength(191)
                            ->placeholder(__('app.placeholder_nom_classe')),
                        
                        Forms\Components\Select::make('niveau')
                            ->label(__('app.niveau'))
                            ->required()
                            ->options(\App\Support\Academic::levelOptionsGrouped())
                            ->searchable()
                            ->live()
                            // Clear the série whenever the level no longer supports one.
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (! \App\Support\Academic::levelHasSeries($state)) {
                                    $set('serie', null);
                                }
                            }),

                        Forms\Components\Select::make('serie')
                            ->label(__('app.serie'))
                            ->options(\App\Support\Academic::serieOptions())
                            ->placeholder(__('app.serie_placeholder'))
                            // Séries exist only at lycée (5AS–7AS).
                            ->visible(fn (Forms\Get $get) => \App\Support\Academic::levelHasSeries($get('niveau')))
                            ->required(fn (Forms\Get $get) => \App\Support\Academic::levelHasSeries($get('niveau'))),
                    ])
                    ->columns(2),
                    
                // Read-only class view for users with view-only permissions
                Forms\Components\Section::make(__('app.class_consultation'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('class.view') && !auth()->user()->hasPermissionTo('class.create') && !auth()->user()->hasPermissionTo('class.edit'))
                    ->schema([
                        Forms\Components\Placeholder::make('nom_classe_display')
                            ->label(__('app.nom_classe'))
                            ->content(fn ($record) => new \Illuminate\Support\HtmlString('<span class="text-lg font-semibold text-blue-600">' . $record->nom_classe . '</span>')),
                            
                        Forms\Components\Placeholder::make('niveau_display')
                            ->label(__('app.niveau'))
                            ->content(fn ($record) => new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-700/10">📚 ' . e(\App\Support\Academic::levelLabel($record->niveau)) . '</span>')),

                        Forms\Components\Placeholder::make('serie_display')
                            ->label(__('app.serie'))
                            ->visible(fn ($record) => filled($record?->serie))
                            ->content(fn ($record) => new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-700/10">' . e($record->serie) . '</span>')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom_classe')
                    ->label(__('app.nom_classe'))
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('niveau')
                    ->label(__('app.niveau'))
                    ->formatStateUsing(fn ($state) => \App\Support\Academic::levelLabel($state))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('serie')
                    ->label(__('app.serie'))
                    ->placeholder('—')
                    ->badge()
                    ->color('warning')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('etudiants_count')
                    ->label(__('app.etudiants'))
                    ->sortable()
                    ->counts('etudiants')
                    ->badge()
                    ->color('success'),
                    
                Tables\Columns\TextColumn::make('cours_count')
                    ->label(__('app.cours'))
                    ->counts('cours')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                    
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
                Tables\Filters\SelectFilter::make('niveau')
                    ->label(__('app.level'))
                    ->options(\App\Support\Academic::levelOptionsGrouped()),

                Tables\Filters\SelectFilter::make('serie')
                    ->label(__('app.serie'))
                    ->options(\App\Support\Academic::serieOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('view-timetable')
                    ->label(__('app.emploi_temps'))
                    ->icon('heroicon-o-calendar-days')
                    ->color('info')
                    ->url(fn (Classe $record): string => static::getUrl('view-timetable', ['record' => $record])),
                    
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListClasses::route('/'),
            'create' => Pages\CreateClasse::route('/create'),
            'edit' => Pages\EditClasse::route('/{record}/edit'),
            'view-timetable' => Pages\ViewClasseTimetable::route('/{record}/timetable')
        ];
    }
}
