<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdministrateurResource\Pages;
use App\Filament\Resources\AdministrateurResource\RelationManagers;
use App\Models\Administrateur;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdministrateurResource extends Resource
{
    protected static ?string $model = Administrateur::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('app.systeme');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.gestion_admins');
    }

    public static function getPluralLabel(): string
    {
        return __('app.gestion_admins');
    }

    public static function getModelLabel(): string
    {
        return __('app.administrateur');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('user.manage');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('user.manage');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasPermissionTo('user.manage');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasPermissionTo('user.manage');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.informations_personnelles'))
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->label(__('app.nom'))
                            ->required()
                            ->maxLength(191),
                            
                        Forms\Components\TextInput::make('prenom')
                            ->label(__('app.prenom'))
                            ->required()
                            ->maxLength(191),
                            
                        Forms\Components\TextInput::make('telephone')
                            ->label(__('app.telephone'))
                            ->tel()
                            ->maxLength(191),
                            
                        Forms\Components\Textarea::make('adresse')
                            ->label(__('app.adresse'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make(__('app.compte_utilisateur'))
                    ->description(__('app.compte_utilisateur_description'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('user.manage'))
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label(__('app.email'))
                            ->email()
                            ->required()
                            ->maxLength(191)
                            ->hiddenOn('view'),
                            
                        Forms\Components\TextInput::make('email_display')
                            ->label(__('app.email'))
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('view')
                            ->default(fn ($record) => $record->user?->email ?? '-'),
                            
                        Forms\Components\TextInput::make('password')
                            ->label(__('app.password'))
                            ->password()
                            ->revealable()
                            ->maxLength(191)
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText(fn (string $context): ?string => $context === 'edit' ? __('app.leave_empty_to_keep_current') : null)
                            ->hiddenOn('view'),

                        Forms\Components\Select::make('role')
                            ->label(__('app.role'))
                            ->options([
                                'admin' => __('app.admin'),
                                'super_admin' => __('app.super_admin'),
                            ])
                            ->required()
                            ->default('admin')
                            ->hiddenOn('view'),
                            
                        Forms\Components\Placeholder::make('role_display')
                            ->label(__('app.role'))
                            ->content(fn ($record) => $record->user?->hasRole('super_admin') 
                                ? __('app.super_admin')
                                : __('app.admin'))
                            ->visibleOn('view'),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('app.compte_actif'))
                            ->default(true)
                            ->hiddenOn('view'),
                            
                        Forms\Components\Placeholder::make('compte_status')
                            ->label(__('app.status'))
                            ->content(fn ($record) => $record->user?->is_active 
                                ? new \Illuminate\Support\HtmlString('<span class="text-success-600 font-semibold">' . __('app.actif') . '</span>')
                                : new \Illuminate\Support\HtmlString('<span class="text-danger-600 font-semibold">' . __('app.inactif') . '</span>'))
                            ->visibleOn('view'),
                    ])
                    ->columns(2),
                    
                // Read-only account information for users without manage users permission
                Forms\Components\Section::make(__('app.compte_utilisateur'))
                    ->description(__('app.account_info_readonly_note'))
                    ->visible(fn () => !auth()->user()->hasPermissionTo('user.manage'))
                    ->schema([
                        Forms\Components\TextInput::make('user.email')
                            ->label(__('app.email'))
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn ($record) => $record->user?->email ?? '-'),
                            
                        Forms\Components\Placeholder::make('role_display')
                            ->label(__('app.role'))
                            ->content(fn ($record) => $record->user?->hasRole('super_admin') 
                                ? new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-700/10">🔐 ' . __('app.super_admin') . '</span>')
                                : new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">👨‍💼 ' . __('app.admin') . '</span>')),
                            
                        Forms\Components\Placeholder::make('compte_status')
                            ->label(__('app.statut_compte'))
                            ->content(fn ($record) => $record->user?->is_active 
                                ? new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-700/10">✅ ' . __('app.actif') . '</span>')
                                : new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-700/10">❌ ' . __('app.inactif') . '</span>')),
                            
                        Forms\Components\Placeholder::make('two_factor_status_readonly')
                            ->label(__('app.authentication_2fa'))
                            ->content(fn ($record) => $record->user?->two_factor_enabled 
                                ? new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-700/10">🔐 ' . __('app.actif') . '</span>')
                                : new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-700/10">🔓 ' . __('app.inactif') . '</span>')),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make(__('app.two_factor'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('user.manage'))
                    ->schema([
                        Forms\Components\Toggle::make('two_factor_enabled')
                            ->label(__('app.deux_facteurs_active_court'))
                            ->default(false)
                            ->hiddenOn('view'),
                            
                        Forms\Components\Placeholder::make('two_factor_status')
                            ->label(__('app.deux_facteurs_active_court'))
                            ->content(fn ($record) => $record->user?->two_factor_enabled 
                                ? new \Illuminate\Support\HtmlString('<span class="text-success-600 font-semibold">✓ ' . __('app.actif') . '</span>')
                                : new \Illuminate\Support\HtmlString('<span class="text-gray-600">✗ ' . __('app.inactif') . '</span>'))
                            ->visibleOn('view'),
                            
                        Forms\Components\Textarea::make('two_factor_recovery_codes')
                            ->label(__('app.recovery_codes'))
                            ->helperText(__('app.codes_recuperation_helper'))
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(8)
                            ->visibleOn('view')
                            ->hidden(fn ($record) => !$record->user?->two_factor_enabled || !$record->user?->two_factor_recovery_codes),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label(__('app.nom'))
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('prenom')
                    ->label(__('app.prenom'))
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.email')
                    ->label(__('app.email'))
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                    
                Tables\Columns\TextColumn::make('user.roles.name')
                    ->label(__('app.role'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __("app.{$state}") : '-')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\IconColumn::make('user.is_active')
                    ->label(__('app.actif'))
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('user.two_factor_enabled')
                    ->label(__('app.two_factor'))
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('warning'),
                    
                Tables\Columns\TextColumn::make('user.last_login_at')
                    ->label(__('app.derniere_connexion'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('app.never')),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.date_creation'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('user.is_active')
                    ->label(__('app.status')),
                Tables\Filters\TernaryFilter::make('user.two_factor_enabled')
                    ->label(__('app.two_factor'))
                    ->placeholder(__('app.voir_tout'))
                    ->trueLabel(__('app.oui'))
                    ->falseLabel(__('app.non')),
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
            'index' => Pages\ListAdministrateurs::route('/'),
            'create' => Pages\CreateAdministrateur::route('/create'),
            'edit' => Pages\EditAdministrateur::route('/{record}/edit'),
            'view' => Pages\ViewAdministrateur::route('/{record}'),
        ];
    }
}
