<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use App\Filament\Resources\EnseignantResource\Pages\ListEnseignants;
use App\Filament\Resources\EnseignantResource\Pages\CreateEnseignant;
use App\Filament\Resources\EnseignantResource\Pages\EditEnseignant;
use App\Filament\Resources\EnseignantResource\Pages\ViewEnseignant;
use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\EnseignantResource\Pages;
use App\Filament\Resources\EnseignantResource\RelationManagers;
use App\Models\Enseignant;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class EnseignantResource extends Resource
{
    use HasRoleBasedAccess;
    
    protected static ?string $model = Enseignant::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('app.personnes');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.enseignants');
    }

    public static function getPluralLabel(): string
    {
        return __('app.enseignants');
    }

    public static function getModelLabel(): string
    {
        return __('app.enseignant');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('teacher.view');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('teacher.create');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('teacher.edit');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->hasPermissionTo('teacher.delete');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.informations_personnelles'))
                    ->schema([
                        TextInput::make('nom')
                            ->label(__('app.nom'))
                            ->required()
                            ->maxLength(191),
                            
                        TextInput::make('prenom')
                            ->label(__('app.prenom'))
                            ->required()
                            ->maxLength(191),
                    ])
                    ->columns(2),

                Section::make(__('app.contact_information'))
                    ->schema([
                        TextInput::make('telephone')
                            ->label(__('app.telephone'))
                            ->tel()
                            ->maxLength(191),
                        
                        Textarea::make('adresse')
                            ->label(__('app.adresse'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                    
                Section::make(__('app.informations_compte'))
                    ->visible(fn () => !auth()->user()->hasPermissionTo('user.manage'))
                    ->description(__('app.account_info_readonly'))
                    ->schema([
                        Placeholder::make('email_readonly')
                            ->label(__('app.email'))
                            ->content(fn ($record) => $record->user?->email ?? __('app.aucun_compte'))
                            ->visibleOn(['edit', 'view']),
                            
                        Placeholder::make('account_status_readonly')
                            ->label(__('app.statut_compte'))
                            ->content(fn ($record) => $record->user?->is_active 
                                ? new HtmlString('<span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">' . __('app.actif') . '</span>')
                                : new HtmlString('<span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">' . __('app.inactif') . '</span>'))
                            ->visibleOn(['edit', 'view']),
                    ])
                    ->columns(2),
                    
                Section::make(__('app.compte_utilisateur'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('user.manage'))
                    ->description(__('app.compte_utilisateur_description'))
                    ->schema([
                        TextInput::make('email')
                            ->label(__('app.email'))
                            ->email()
                            ->maxLength(191)
                            ->helperText(__('app.leave_empty_for_no_account'))
                            ->hiddenOn('view'),
                            
                        TextInput::make('email_display')
                            ->label(__('app.email'))
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('view')
                            ->default(fn ($record) => $record->user?->email ?? '-'),
                                    
                        TextInput::make('password')
                            ->label(__('app.password'))
                            ->password()
                            ->revealable()
                            ->maxLength(191)
                            ->helperText(__('app.leave_empty_to_keep_current'))
                            ->hiddenOn('view'),
                                    
                        Toggle::make('is_active')
                            ->label(__('app.compte_actif'))
                            ->default(true)
                            ->hiddenOn('view'),
                            
                        Placeholder::make('compte_status')
                            ->label(__('app.status'))
                            ->content(fn ($record) => $record->user?->is_active 
                                ? new HtmlString('<span class="text-success-600 font-semibold">' . __('app.actif') . '</span>')
                                : new HtmlString('<span class="text-danger-600 font-semibold">' . __('app.inactif') . '</span>'))
                            ->visibleOn('view'),
                    ])
                    ->columns(2),

                Section::make(__('app.assignments'))
                    ->schema([
                        Placeholder::make('matieres_list')
                            ->label(__('app.matieres'))
                            ->content(fn (callable $get) => filled($get('matieres_list'))
                                ? new HtmlString($get('matieres_list'))
                                : __('app.no_subject'))
                            ->extraAttributes(['class' => 'gap-2 flex flex-wrap']),
                        
                        Placeholder::make('classes_list')
                            ->label(__('app.classes_assignees'))
                            ->content(fn (callable $get) => filled($get('classes_list'))
                                ? new HtmlString($get('classes_list'))
                                : __('app.aucune_classe_assignee'))
                            ->extraAttributes(['class' => 'gap-2 flex flex-wrap']),
                    ])
                    ->columns(1)
                    ->visibleOn('view'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')
                    ->label(__('app.nom'))
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('prenom')
                    ->label(__('app.prenom'))
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('user.email')
                    ->label(__('app.email'))
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->default('-'),
                    
                TextColumn::make('telephone')
                    ->label(__('app.telephone'))
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),

                TextColumn::make('matieres')
                    ->label(__('app.matieres'))
                    ->formatStateUsing(fn ($record) => $record->matieres->pluck('nom_matiere')->join(', ') ?: __('app.no_subject'))
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('cours_count')
                    ->label(__('app.cours'))
                    ->counts('cours')
                    ->badge()
                    ->color('info'),
                    
                IconColumn::make('user.is_active')
                    ->label(__('app.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->default(false),
                    
                TextColumn::make('user.last_login_at')
                    ->label(__('app.derniere_connexion'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('app.never')),
                    
                TextColumn::make('created_at')
                    ->label(__('app.date_creation'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('user.is_active')
                    ->label(__('app.status'))
                    ->placeholder(__('app.voir_tout'))
                    ->trueLabel(__('app.oui'))
                    ->falseLabel(__('app.non')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn () => auth()->user()->hasPermissionTo('teacher.edit')),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()->hasPermissionTo('teacher.delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasPermissionTo('teacher.delete')),
                    BulkAction::make('activate')
                        ->label(__('app.activer'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->user?->update(['is_active' => true])))
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn () => auth()->user()->hasPermissionTo('user.manage')),
                    BulkAction::make('deactivate')
                        ->label(__('app.desactiver'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->user?->update(['is_active' => false])))
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn () => auth()->user()->hasPermissionTo('user.manage')),
                ]),
            ])
            ->defaultSort('nom', 'asc');
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
            'index' => ListEnseignants::route('/'),
            'create' => CreateEnseignant::route('/create'),
            'edit' => EditEnseignant::route('/{record}/edit'),
            'view' => ViewEnseignant::route('/{record}'),
        ];
    }
}
