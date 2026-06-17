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
use Filament\Notifications\Notification;
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
                    ->icon('heroicon-o-user')
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
                    ->icon('heroicon-o-key')
                    ->visible(fn () => auth()->user()->hasPermissionTo('user.manage'))
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label(__('app.email'))
                            ->email()
                            ->required()
                            ->maxLength(191)
                            // Validate against users.email; ignore this admin's own linked user on edit.
                            ->unique(table: 'users', column: 'email', ignorable: fn ($record) => $record?->user)
                            ->hiddenOn('view'),
                            
                        Forms\Components\TextInput::make('email_display')
                            ->label(__('app.email'))
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('view')
                            ->default(fn ($record) => $record->user?->email ?? '-'),
                            
                        // Create-only: on edit, password is changed via the "Reset password" button.
                        Forms\Components\TextInput::make('password')
                            ->label(__('app.password'))
                            ->password()
                            ->revealable()
                            ->maxLength(191)
                            ->minLength(8)
                            ->required()
                            ->visibleOn('create'),

                        Forms\Components\Select::make('role')
                            ->label(__('app.role'))
                            ->options([
                                'super_admin' => __('app.super_admin'),
                                'admin' => __('app.admin'),
                                'director' => __('app.director'),
                                'academic_coordinator' => __('app.academic_coordinator'),
                                'secretary' => __('app.secretary'),
                                'accountant' => __('app.accountant'),
                            ])
                            ->required()
                            ->default('admin')
                            ->hiddenOn('view'),

                        Forms\Components\Placeholder::make('role_display')
                            ->label(__('app.role'))
                            ->content(fn ($record) => $record->user?->roles
                                ->pluck('name')
                                ->map(fn ($r) => __("app.{$r}"))
                                ->join(', ') ?: '-')
                            ->visibleOn('view'),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('app.compte_actif'))
                            ->default(true)
                            ->hiddenOn('view'),
                            
                        Forms\Components\Placeholder::make('compte_status')
                            ->label(__('app.status'))
                            ->content(fn ($record) => $record->user?->is_active
                                ? new \Illuminate\Support\HtmlString('<span class="font-semibold text-success-600 dark:text-success-400">' . __('app.actif') . '</span>')
                                : new \Illuminate\Support\HtmlString('<span class="font-semibold text-danger-600 dark:text-danger-400">' . __('app.inactif') . '</span>'))
                            ->visibleOn('view'),
                    ])
                    ->columns(2),
                    
                // Read-only account information for users without manage users permission
                Forms\Components\Section::make(__('app.compte_utilisateur'))
                    ->description(__('app.account_info_readonly_note'))
                    ->icon('heroicon-o-key')
                    ->visible(fn () => !auth()->user()->hasPermissionTo('user.manage'))
                    ->schema([
                        Forms\Components\TextInput::make('user.email')
                            ->label(__('app.email'))
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn ($record) => $record->user?->email ?? '-'),
                            
                        Forms\Components\Placeholder::make('role_display')
                            ->label(__('app.role'))
                            ->content(function ($record) {
                                $roleName = $record->user?->roles->pluck('name')->first();
                                $label = $roleName ? __("app.{$roleName}") : '-';
                                $classes = $roleName === 'super_admin'
                                    ? 'bg-purple-50 text-purple-700 ring-purple-700/10 dark:bg-purple-400/10 dark:text-purple-400 dark:ring-purple-400/30'
                                    : 'bg-blue-50 text-blue-700 ring-blue-700/10 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/30';

                                return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset ' . $classes . '">' . ($roleName === 'super_admin' ? '🔐 ' : '👤 ') . e($label) . '</span>');
                            }),

                        Forms\Components\Placeholder::make('compte_status')
                            ->label(__('app.statut_compte'))
                            ->content(fn ($record) => $record->user?->is_active
                                ? new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-700/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">✅ ' . __('app.actif') . '</span>')
                                : new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-700/10 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30">❌ ' . __('app.inactif') . '</span>')),

                        Forms\Components\Placeholder::make('two_factor_status_readonly')
                            ->label(__('app.authentication_2fa'))
                            ->content(fn ($record) => $record->user?->two_factor_enabled
                                ? new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-700/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">🔐 ' . __('app.actif') . '</span>')
                                : new \Illuminate\Support\HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-700/10 dark:bg-white/5 dark:text-gray-400 dark:ring-white/10">🔓 ' . __('app.inactif') . '</span>')),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make(__('app.two_factor'))
                    ->icon('heroicon-o-shield-check')
                    ->visible(fn () => auth()->user()->hasPermissionTo('user.manage'))
                    ->schema([
                        Forms\Components\Toggle::make('two_factor_enabled')
                            ->label(__('app.deux_facteurs_active_court'))
                            ->default(false)
                            ->hiddenOn('view'),
                            
                        Forms\Components\Placeholder::make('two_factor_status')
                            ->label(__('app.deux_facteurs_active_court'))
                            ->content(fn ($record) => $record->user?->two_factor_enabled
                                ? new \Illuminate\Support\HtmlString('<span class="font-semibold text-success-600 dark:text-success-400">✓ ' . __('app.actif') . '</span>')
                                : new \Illuminate\Support\HtmlString('<span class="text-gray-600 dark:text-gray-400">✗ ' . __('app.inactif') . '</span>'))
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
                Tables\Actions\Action::make('resetPassword')
                    ->label(__('app.reset_password'))
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->hasPermissionTo('user.manage'))
                    ->modalHeading(fn ($record) => __('app.reset_password') . ' — ' . trim(($record->prenom ?? '') . ' ' . ($record->nom ?? '')))
                    ->modalSubmitActionLabel(__('app.reset_password'))
                    ->form(self::passwordResetFormSchema())
                    ->action(fn (Administrateur $record, array $data) => self::applyPasswordReset($record, $data)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nom', 'asc');
    }

    /** Shared "reset password" modal form (used by the list action and the edit header). */
    public static function passwordResetFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('password')
                ->label(__('app.new_password'))
                ->password()
                ->revealable()
                ->required()
                ->minLength(8)
                ->confirmed(),
            Forms\Components\TextInput::make('password_confirmation')
                ->label(__('app.confirm_new_password'))
                ->password()
                ->revealable()
                ->required(),
        ];
    }

    /** Apply a password reset to the admin's linked user account. */
    public static function applyPasswordReset(Administrateur $record, array $data): void
    {
        $user = $record->user;

        if (! $user) {
            Notification::make()->title(__('app.no_linked_account'))->danger()->send();

            return;
        }

        // User model casts password => 'hashed', so pass plain (hashed once by the cast).
        $user->update(['password' => $data['password']]);

        Notification::make()->title(__('app.password_updated'))->success()->send();
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
