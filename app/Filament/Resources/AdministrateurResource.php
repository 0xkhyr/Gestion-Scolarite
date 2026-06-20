<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use App\Services\ActivityLogger;
use App\Filament\Resources\AdministrateurResource\Pages\ListAdministrateurs;
use App\Filament\Resources\AdministrateurResource\Pages\CreateAdministrateur;
use App\Filament\Resources\AdministrateurResource\Pages\EditAdministrateur;
use App\Filament\Resources\AdministrateurResource\Pages\ViewAdministrateur;
use App\Filament\Resources\AdministrateurResource\Pages;
use App\Filament\Resources\AdministrateurResource\RelationManagers;
use App\Models\Administrateur;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdministrateurResource extends Resource
{
    protected static ?string $model = Administrateur::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';
    
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.informations_personnelles'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('nom')
                            ->label(__('app.nom'))
                            ->required()
                            ->maxLength(191),
                            
                        TextInput::make('prenom')
                            ->label(__('app.prenom'))
                            ->required()
                            ->maxLength(191),
                            
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
                    
                Section::make(__('app.compte_utilisateur'))
                    ->description(__('app.compte_utilisateur_description'))
                    ->icon('heroicon-o-key')
                    ->visible(fn () => auth()->user()->hasPermissionTo('user.manage'))
                    ->schema([
                        TextInput::make('email')
                            ->label(__('app.email'))
                            ->email()
                            ->required()
                            ->maxLength(191)
                            // Validate against users.email; ignore this admin's own linked user on edit.
                            ->unique(table: 'users', column: 'email', ignorable: fn ($record) => $record?->user)
                            ->hiddenOn('view'),
                            
                        TextInput::make('email_display')
                            ->label(__('app.email'))
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('view')
                            ->default(fn ($record) => $record->user?->email ?? '-'),
                            
                        // Create-only: on edit, password is changed via the "Reset password" button.
                        TextInput::make('password')
                            ->label(__('app.password'))
                            ->password()
                            ->revealable()
                            ->maxLength(191)
                            ->minLength(8)
                            ->required()
                            ->visibleOn('create'),

                        Select::make('role')
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

                        Placeholder::make('role_display')
                            ->label(__('app.role'))
                            ->content(fn ($record) => $record->user?->roles
                                ->pluck('name')
                                ->map(fn ($r) => __("app.{$r}"))
                                ->join(', ') ?: '-')
                            ->visibleOn('view'),

                        Toggle::make('is_active')
                            ->label(__('app.compte_actif'))
                            ->default(true)
                            ->hiddenOn('view'),
                            
                        Placeholder::make('compte_status')
                            ->label(__('app.status'))
                            ->content(fn ($record) => $record->user?->is_active
                                ? self::badge('success', __('app.actif'))
                                : self::badge('danger', __('app.inactif')))
                            ->visibleOn('view'),
                    ])
                    ->columns(2),
                    
                // Read-only account information for users without manage users permission
                Section::make(__('app.compte_utilisateur'))
                    ->description(__('app.account_info_readonly_note'))
                    ->icon('heroicon-o-key')
                    ->visible(fn () => !auth()->user()->hasPermissionTo('user.manage'))
                    ->schema([
                        TextInput::make('user.email')
                            ->label(__('app.email'))
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn ($record) => $record->user?->email ?? '-'),
                            
                        Placeholder::make('role_display')
                            ->label(__('app.role'))
                            ->content(function ($record) {
                                $roleName = $record->user?->roles->pluck('name')->first();
                                $label = $roleName ? __("app.{$roleName}") : '-';

                                return self::badge(
                                    $roleName === 'super_admin' ? 'primary' : 'info',
                                    $label,
                                );
                            }),

                        Placeholder::make('compte_status')
                            ->label(__('app.statut_compte'))
                            ->content(fn ($record) => $record->user?->is_active
                                ? self::badge('success', __('app.actif'))
                                : self::badge('danger', __('app.inactif'))),

                        Placeholder::make('two_factor_status_readonly')
                            ->label(__('app.authentication_2fa'))
                            ->content(fn ($record) => $record->user?->two_factor_enabled
                                ? self::badge('success', __('app.actif'))
                                : self::badge('gray', __('app.inactif'))),
                    ])
                    ->columns(2),
                    
                Section::make(__('app.two_factor'))
                    ->icon('heroicon-o-shield-check')
                    ->visible(fn () => auth()->user()->hasPermissionTo('user.manage'))
                    ->schema([
                        Toggle::make('two_factor_enabled')
                            ->label(__('app.deux_facteurs_active_court'))
                            ->default(false)
                            ->hiddenOn('view'),
                            
                        Placeholder::make('two_factor_status')
                            ->label(__('app.deux_facteurs_active_court'))
                            ->content(fn ($record) => $record->user?->two_factor_enabled
                                ? self::badge('success', __('app.actif'))
                                : self::badge('gray', __('app.inactif')))
                            ->visibleOn('view'),
                            
                        Textarea::make('two_factor_recovery_codes')
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
                    ->icon('heroicon-o-envelope'),
                    
                TextColumn::make('user.roles.name')
                    ->label(__('app.role'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __("app.{$state}") : '-')
                    ->badge()
                    ->color('primary'),

                IconColumn::make('user.is_active')
                    ->label(__('app.actif'))
                    ->boolean()
                    ->sortable(),
                    
                IconColumn::make('user.two_factor_enabled')
                    ->label(__('app.two_factor'))
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('warning'),
                    
                TextColumn::make('user.last_login_at')
                    ->label(__('app.derniere_connexion'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('app.never')),
                    
                TextColumn::make('created_at')
                    ->label(__('app.date_creation'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('user.is_active')
                    ->label(__('app.status')),
                TernaryFilter::make('user.two_factor_enabled')
                    ->label(__('app.two_factor'))
                    ->placeholder(__('app.voir_tout'))
                    ->trueLabel(__('app.oui'))
                    ->falseLabel(__('app.non')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('resetPassword')
                    ->label(__('app.reset_password'))
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->hasPermissionTo('user.manage'))
                    ->modalHeading(fn ($record) => __('app.reset_password') . ' — ' . trim(($record->prenom ?? '') . ' ' . ($record->nom ?? '')))
                    ->modalSubmitActionLabel(__('app.reset_password'))
                    ->schema(self::passwordResetFormSchema())
                    ->action(fn (Administrateur $record, array $data) => self::applyPasswordReset($record, $data)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nom', 'asc');
    }

    /**
     * Render a native Filament badge as HTML (colors come from Filament's own CSS).
     * Wrapped in a flex container so it shrinks to its content instead of
     * stretching to the placeholder's full width.
     */
    protected static function badge(string $color, string $label): HtmlString
    {
        return new HtmlString(
            Blade::render(
                '<div class="flex"><x-filament::badge :color="$color">{{ $label }}</x-filament::badge></div>',
                ['color' => $color, 'label' => $label],
            )
        );
    }

    /** Shared "reset password" modal form (used by the list action and the edit header). */
    public static function passwordResetFormSchema(): array
    {
        return [
            TextInput::make('password')
                ->label(__('app.new_password'))
                ->password()
                ->revealable()
                ->required()
                ->minLength(8)
                ->confirmed(),
            TextInput::make('password_confirmation')
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

        ActivityLogger::record(
            'security',
            "Password reset for {$user->name} ({$user->email})",
            $user,
            ['type' => 'password_reset', 'target_user_id' => $user->id],
        );

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
            'index' => ListAdministrateurs::route('/'),
            'create' => CreateAdministrateur::route('/create'),
            'edit' => EditAdministrateur::route('/{record}/edit'),
            'view' => ViewAdministrateur::route('/{record}'),
        ];
    }
}
