<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Support\Academic;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use App\Filament\Resources\MatiereResource\Pages\ListMatieres;
use App\Filament\Resources\MatiereResource\Pages\CreateMatiere;
use App\Filament\Resources\MatiereResource\Pages\EditMatiere;
use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\MatiereResource\Pages;
use App\Filament\Resources\MatiereResource\RelationManagers;
use App\Models\Matiere;
use Filament\Forms;
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

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.informations_matiere'))
                    ->schema([
                        TextInput::make('nom_matiere')
                            ->label(__('app.nom_matiere'))
                            ->required()
                            ->maxLength(191)
                            ->placeholder(__('app.placeholder_nom_matiere')),
                            
                        TextInput::make('code_matiere')
                            ->label(__('app.code_matiere'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(191)
                            ->placeholder(__('app.placeholder_code_matiere')),
                            
                        TextInput::make('coefficient')
                            ->label(__('app.coefficient'))
                            ->helperText(__('app.coefficient_global_helper'))
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(10),

                        TextInput::make('note_max')
                            ->label(__('app.note_max'))
                            ->helperText(__('app.note_max_helper'))
                            ->required()
                            ->numeric()
                            ->default(20)
                            ->minValue(1)
                            ->maxValue(100),

                        Toggle::make('active')
                            ->label(__('app.actif'))
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make(__('app.serie_coefficients_section'))
                    ->description(__('app.serie_coefficients_desc'))
                    ->collapsible()
                    ->schema(
                        collect(Academic::serieOptions())
                            ->map(fn ($label, $code) => TextInput::make('serie_coefficients.' . $code)
                                ->label($label)
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(20)
                                ->placeholder(__('app.serie_coefficient_fallback')))
                            ->values()
                            ->all()
                    )
                    ->columns(2),

                Section::make(__('app.description'))
                    ->schema([
                        Textarea::make('description')
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
                TextColumn::make('code_matiere')
                    ->label(__('app.code_matiere'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                    
                
                TextColumn::make('nom_matiere')
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
                
                    
                TextColumn::make('coefficient')
                    ->label(__('app.coefficient'))

                    ->sortable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('note_max')
                    ->label(__('app.note_max'))
                    ->formatStateUsing(fn ($state) => '/' . rtrim(rtrim(number_format((float) $state, 2, '.', ''), '0'), '.'))
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                IconColumn::make('active')
                    ->label(__('app.actif'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
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
                TernaryFilter::make('active')
                    ->label(__('app.actif'))
                    ->placeholder(__('app.all_subjects'))
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('activate')
                        ->label(__('app.activate'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('deactivate')
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
            'index' => ListMatieres::route('/'),
            'create' => CreateMatiere::route('/create'),
            'edit' => EditMatiere::route('/{record}/edit'),
        ];
    }
}
