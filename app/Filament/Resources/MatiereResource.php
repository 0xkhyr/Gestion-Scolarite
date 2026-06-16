<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\MatiereResource\Pages;
use App\Filament\Resources\MatiereResource\RelationManagers;
use App\Models\Matiere;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MatiereResource extends Resource
{
    use HasRoleBasedAccess;
    
    protected static ?string $model = Matiere::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('app.gestion_academique');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.matieres');
    }

    public static function getPluralLabel(): string
    {
        return __('app.matieres');
    }

    public static function getModelLabel(): string
    {
        return __('app.matiere');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('subject.view');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('subject.create');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('subject.edit');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('subject.delete');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.informations_matiere'))
                    ->schema([
                        Forms\Components\TextInput::make('nom_matiere')
                            ->label(__('app.nom_matiere'))
                            ->required()
                            ->maxLength(191)
                            ->placeholder(__('app.placeholder_nom_matiere')),
                            
                        Forms\Components\TextInput::make('code_matiere')
                            ->label(__('app.code_matiere'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(191)
                            ->placeholder(__('app.placeholder_code_matiere')),
                            
                        Forms\Components\TextInput::make('coefficient')
                            ->label(__('app.coefficient'))
                            ->helperText(__('app.coefficient_global_helper'))
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(10),

                        Forms\Components\TextInput::make('note_max')
                            ->label(__('app.note_max'))
                            ->helperText(__('app.note_max_helper'))
                            ->required()
                            ->numeric()
                            ->default(20)
                            ->minValue(1)
                            ->maxValue(100),

                        Forms\Components\Toggle::make('active')
                            ->label(__('app.actif'))
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('app.serie_coefficients_section'))
                    ->description(__('app.serie_coefficients_desc'))
                    ->collapsible()
                    ->schema(
                        collect(\App\Support\Academic::serieOptions())
                            ->map(fn ($label, $code) => Forms\Components\TextInput::make('serie_coefficients.' . $code)
                                ->label($label)
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(20)
                                ->placeholder(__('app.serie_coefficient_fallback')))
                            ->values()
                            ->all()
                    )
                    ->columns(2),

                Forms\Components\Section::make(__('app.description'))
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label(__('app.description'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code_matiere')
                    ->label(__('app.code_matiere'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                    
                
                Tables\Columns\TextColumn::make('nom_matiere')
                    ->label(__('app.nom_matiere'))
                    ->formatStateUsing(fn ($record) =>
                            !empty($record->code_matiere)
                                ? __("app." . $record->code_matiere)
                                : $record->nom_matiere
                        )
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                    
                Tables\Columns\TextColumn::make('coefficient')
                    ->label(__('app.coefficient'))

                    ->sortable()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('note_max')
                    ->label(__('app.note_max'))
                    ->formatStateUsing(fn ($state) => '/' . rtrim(rtrim(number_format((float) $state, 2, '.', ''), '0'), '.'))
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('active')
                    ->label(__('app.actif'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
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
                Tables\Filters\TernaryFilter::make('active')
                    ->label(__('app.actif'))
                    ->placeholder(__('app.all_subjects'))
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label(__('app.activate'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label(__('app.deactivate'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('nom_matiere', 'asc');
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
            'index' => Pages\ListMatieres::route('/'),
            'create' => Pages\CreateMatiere::route('/create'),
            'edit' => Pages\EditMatiere::route('/{record}/edit'),
        ];
    }
}
