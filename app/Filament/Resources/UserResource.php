<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Services\ActivityLogger;
use Filament\Forms;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

/**
 * Central account hub for the single users table. Every login lives here
 * (admins, teachers, students via polymorphic profile_type). Profile/domain
 * data is edited in the per-type resources; this resource manages the
 * account/login layer: status, password, roles and 2FA — addressed by user id.
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('app.systeme');
    }

    // Only account managers see the sidebar item. Audit-only viewers
    // (activity_log.view) can still open a causer's profile via its link.
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasPermissionTo('user.manage') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return __('app.users');
    }

    public static function getModelLabel(): string
    {
        return __('app.user');
    }

    public static function getPluralLabel(): string
    {
        return __('app.users');
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasPermissionTo('activity_log.view')
            || $user->hasPermissionTo('user.manage');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    /**
     * Whether the current user may perform account actions on $record.
     * Requires user.manage; you can't act on your own account here, and only a
     * super admin may manage another super admin.
     */
    public static function canManage(User $record): bool
    {
        $actor = auth()->user();

        if (! $actor?->hasPermissionTo('user.manage')) {
            return false;
        }

        if ($actor->id === $record->id) {
            return false;
        }

        if ($record->hasRole('super_admin') && ! $actor->hasRole('super_admin')) {
            return false;
        }

        return true;
    }

    /** Shared "reset password" form schema (used by the table + view page). */
    public static function passwordResetSchema(): array
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

    /** Shared "manage roles" form schema (used by the table + view page). */
    public static function rolesSchema(): array
    {
        return [
            Forms\Components\CheckboxList::make('roles')
                ->label(__('app.roles'))
                ->options(fn () => Role::query()
                    ->orderBy('name')
                    ->pluck('name', 'name')
                    ->map(fn ($name) => __('app.' . $name))
                    ->toArray())
                ->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('app.full_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('app.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('app.role'))
                    ->badge()
                    ->color(fn ($state) => $state === 'super_admin' ? 'primary' : 'info')
                    ->formatStateUsing(fn ($state) => __('app.' . $state)),
                Tables\Columns\TextColumn::make('profile_type')
                    ->label(__('app.profile_type'))
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => $state ? __('app.' . strtolower(class_basename($state))) : null)
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('app.account_status'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label(__('app.last_login'))
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label(__('app.role'))
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => __('app.' . $record->name))
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('profile_type')
                    ->label(__('app.profile_type'))
                    ->options(fn () => User::query()
                        ->distinct()
                        ->whereNotNull('profile_type')
                        ->pluck('profile_type', 'profile_type')
                        ->mapWithKeys(fn ($v) => [$v => __('app.' . strtolower(class_basename($v)))])
                        ->toArray()),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('app.account_status')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),

                    Tables\Actions\Action::make('toggleActive')
                        ->label(fn (User $record) => $record->is_active ? __('app.deactivate') : __('app.activate'))
                        ->icon(fn (User $record) => $record->is_active ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->visible(fn (User $record) => self::canManage($record))
                        ->action(fn (User $record) => self::toggleActive($record)),

                    Tables\Actions\Action::make('resetPassword')
                        ->label(__('app.reset_password'))
                        ->icon('heroicon-o-key')
                        ->color('gray')
                        ->visible(fn (User $record) => self::canManage($record))
                        ->modalHeading(fn (User $record) => __('app.reset_password') . ' — ' . $record->name)
                        ->modalSubmitActionLabel(__('app.reset_password'))
                        ->form(self::passwordResetSchema())
                        ->action(fn (User $record, array $data) => self::resetPassword($record, $data)),

                    Tables\Actions\Action::make('manageRoles')
                        ->label(__('app.manage_roles'))
                        ->icon('heroicon-o-shield-check')
                        ->color('gray')
                        ->visible(fn (User $record) => self::canManage($record))
                        ->fillForm(fn (User $record) => ['roles' => $record->roles->pluck('name')->all()])
                        ->form(self::rolesSchema())
                        ->action(fn (User $record, array $data) => self::syncRoles($record, $data)),

                    Tables\Actions\Action::make('disable2fa')
                        ->label(__('app.force_disable_2fa'))
                        ->icon('heroicon-o-shield-exclamation')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->visible(fn (User $record) => self::canManage($record) && self::hasTwoFactor($record))
                        ->action(fn (User $record) => self::disable2fa($record)),

                    Tables\Actions\Action::make('requireTwoFactor')
                        ->label(fn (User $record) => $record->two_factor_required
                            ? __('app.cancel_2fa_requirement')
                            : __('app.require_2fa_setup'))
                        ->icon('heroicon-o-shield-check')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalDescription(__('app.require_2fa_help'))
                        ->visible(fn (User $record) => self::canManage($record) && ! self::hasTwoFactor($record))
                        ->action(fn (User $record) => self::setTwoFactorRequired($record, ! $record->two_factor_required)),

                    Tables\Actions\Action::make('unlock')
                        ->label(__('app.unlock_account'))
                        ->icon('heroicon-o-lock-open')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->visible(fn (User $record) => self::canManage($record) && self::isLocked($record))
                        ->action(fn (User $record) => self::unlockAccount($record)),

                    Tables\Actions\Action::make('forceLogout')
                        ->label(__('app.force_logout'))
                        ->icon('heroicon-o-arrow-right-on-rectangle')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalDescription(__('app.force_logout_help'))
                        ->visible(fn (User $record) => self::canManage($record))
                        ->action(fn (User $record) => self::forceLogout($record)),

                    Tables\Actions\Action::make('activityTrail')
                        ->label(__('app.activity_trail'))
                        ->icon('heroicon-o-list-bullet')
                        ->color('gray')
                        ->url(fn (User $record) => self::activityTrailUrl($record))
                        ->visible(fn (User $record) => self::activityTrailUrl($record) !== null),

                    Tables\Actions\Action::make('viewProfile')
                        ->label(__('app.view_linked_profile'))
                        ->icon('heroicon-o-identification')
                        ->color('gray')
                        ->url(fn (User $record) => self::profileUrl($record))
                        ->visible(fn (User $record) => self::profileUrl($record) !== null),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label(__('app.activate'))
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn (User $r) => self::canManage($r) ? self::setActive($r, true) : null))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label(__('app.deactivate'))
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn (User $r) => self::canManage($r) ? self::setActive($r, false) : null))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }

    /** Toggle account active state (single-row action). */
    public static function toggleActive(User $record): void
    {
        self::setActive($record, ! $record->is_active);

        Notification::make()
            ->title($record->is_active ? __('app.account_activated') : __('app.account_status_updated'))
            ->success()
            ->send();
    }

    /** Set account active state + audit it. */
    public static function setActive(User $record, bool $active): void
    {
        if ($record->is_active === $active) {
            return;
        }

        $record->update(['is_active' => $active]);

        ActivityLogger::record(
            'security',
            ($active ? 'Account activated: ' : 'Account deactivated: ') . $record->email,
            $record,
            ['type' => $active ? 'account_activated' : 'account_deactivated'],
        );
    }

    /** Reset an account password (User cast hashes it once) + audit it. */
    public static function resetPassword(User $record, array $data): void
    {
        $record->update(['password' => $data['password']]);

        ActivityLogger::record(
            'security',
            "Password reset for {$record->name} ({$record->email})",
            $record,
            ['type' => 'password_reset', 'target_user_id' => $record->id],
        );

        Notification::make()->title(__('app.password_updated'))->success()->send();
    }

    /** Sync roles — the Spatie events fire and are audited by LogPermissionChange. */
    public static function syncRoles(User $record, array $data): void
    {
        $record->syncRoles($data['roles'] ?? []);

        Notification::make()->title(__('app.roles_updated'))->success()->send();
    }

    /** Force-disable 2FA on an account + audit it. */
    public static function disable2fa(User $record): void
    {
        $record->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_enabled' => false,
        ])->save();

        ActivityLogger::record(
            'security',
            "Two-factor authentication force-disabled for {$record->email}",
            $record,
            ['type' => '2fa_force_disabled'],
        );

        Notification::make()->title(__('app.2fa_disabled_title'))->warning()->send();
    }

    /**
     * Require (or stop requiring) this user to enrol in 2FA. You can't enable 2FA
     * for someone — enrolment needs their authenticator — so this forces them to
     * set it up themselves at next login (enforced by EnsureTwoFactorIsVerified).
     */
    public static function setTwoFactorRequired(User $record, bool $required): void
    {
        $record->forceFill(['two_factor_required' => $required])->save();

        ActivityLogger::record(
            'security',
            ($required ? '2FA enrolment required for ' : '2FA requirement removed for ') . $record->email,
            $record,
            ['type' => $required ? '2fa_required' : '2fa_requirement_removed'],
        );

        Notification::make()
            ->title($required ? __('app.two_factor_required_done') : __('app.two_factor_requirement_removed'))
            ->success()
            ->send();
    }

    /**
     * Has the user actually enrolled in 2FA? `two_factor_confirmed_at` is the
     * source of truth (set on enrolment and checked by the enforcement
     * middleware); the two_factor_enabled column is not reliably kept in sync.
     */
    public static function hasTwoFactor(User $record): bool
    {
        return filled($record->two_factor_confirmed_at);
    }

    /** Is the account currently locked out (brute-force) or carrying failed attempts? */
    public static function isLocked(User $record): bool
    {
        return ($record->locked_until && $record->locked_until->isFuture())
            || (int) $record->failed_login_attempts > 0;
    }

    /** Release a brute-force lockout: clear the lock window + failed attempts. */
    public static function unlockAccount(User $record): void
    {
        $record->forceFill([
            'locked_until' => null,
            'failed_login_attempts' => 0,
            'last_failed_login_at' => null,
        ])->save();

        ActivityLogger::record(
            'security',
            "Account unlocked: {$record->email}",
            $record,
            ['type' => 'account_unlocked'],
        );

        Notification::make()->title(__('app.account_unlocked_done'))->success()->send();
    }

    /**
     * Revoke the account's access: delete API (Sanctum) tokens, rotate the
     * remember-me token, and drop DB session rows when that driver is in use.
     * (With the file session driver, the active browser session ends on its next
     * password-bound check / expiry; API + remember-me access is killed now.)
     */
    public static function forceLogout(User $record): void
    {
        $record->tokens()->delete();

        $record->forceFill(['remember_token' => \Illuminate\Support\Str::random(60)])->save();

        if (config('session.driver') === 'database') {
            \Illuminate\Support\Facades\DB::table(config('session.table', 'sessions'))
                ->where('user_id', $record->id)
                ->delete();
        }

        ActivityLogger::record(
            'security',
            "Sessions & tokens revoked for {$record->email}",
            $record,
            ['type' => 'force_logout'],
        );

        Notification::make()->title(__('app.sessions_revoked_done'))->success()->send();
    }

    /** Link to this user's activity-log trail (filtered by causer). */
    public static function activityTrailUrl(User $record): ?string
    {
        if (! (auth()->user()?->hasPermissionTo('activity_log.view') ?? false)) {
            return null;
        }

        return ActivityLogResource::getUrl('index', [
            'tableFilters' => ['causer_id' => ['value' => $record->id]],
        ]);
    }

    /** Link to the account's linked profile record (Administrateur/Enseignant/Etudiant). */
    public static function profileUrl(User $record): ?string
    {
        $map = [
            \App\Models\Administrateur::class => AdministrateurResource::class,
            \App\Models\Enseignant::class => EnseignantResource::class,
            \App\Models\Etudiant::class => EtudiantResource::class,
        ];

        $resource = $map[$record->profile_type] ?? null;

        if (! $resource || ! $record->profile_id || ! $resource::canViewAny()) {
            return null;
        }

        return $resource::getUrl('view', ['record' => $record->profile_id]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make(__('app.profile_information'))
                ->icon('heroicon-o-user')
                ->schema([
                    TextEntry::make('name')->label(__('app.full_name')),
                    TextEntry::make('email')->label(__('app.email'))->copyable(),
                    TextEntry::make('telephone')
                        ->label(__('app.phone_number'))
                        ->placeholder('—'),
                ])
                ->columns(3),

            Section::make(__('app.account_details'))
                ->icon('heroicon-o-identification')
                ->schema([
                    TextEntry::make('roles.name')
                        ->label(__('app.role'))
                        ->badge()
                        ->color(fn ($state) => $state === 'super_admin' ? 'primary' : 'info')
                        ->formatStateUsing(fn ($state) => __('app.' . $state))
                        ->placeholder(__('app.no_roles_assigned')),

                    TextEntry::make('profile_type')
                        ->label(__('app.profile_type'))
                        ->badge()
                        ->color('gray')
                        ->formatStateUsing(fn ($state) => $state
                            ? __('app.' . strtolower(class_basename($state)))
                            : __('app.no_profile_linked'))
                        ->placeholder(__('app.no_profile_linked')),

                    TextEntry::make('is_active')
                        ->label(__('app.account_status'))
                        ->badge()
                        ->color(fn ($state) => $state ? 'success' : 'danger')
                        ->formatStateUsing(fn ($state) => $state ? __('app.active') : __('app.inactive')),

                    TextEntry::make('two_factor_enabled')
                        ->label(__('app.authentication_2fa'))
                        ->badge()
                        ->color(fn ($record) => match (true) {
                            self::hasTwoFactor($record) => 'success',
                            (bool) $record->two_factor_required => 'warning',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn ($record) => match (true) {
                            self::hasTwoFactor($record) => __('app.actif'),
                            (bool) $record->two_factor_required => __('app.required_not_setup'),
                            default => __('app.inactif'),
                        }),

                    TextEntry::make('created_at')
                        ->label(__('app.account_created'))
                        ->dateTime('d M Y'),

                    TextEntry::make('last_login_at')
                        ->label(__('app.last_login'))
                        ->dateTime('d M Y H:i')
                        ->placeholder(__('app.never')),
                ])
                ->columns(3),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
