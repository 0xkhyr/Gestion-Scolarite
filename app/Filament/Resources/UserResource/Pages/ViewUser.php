<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Surface the account actions on the view-page header, collapsed into a single
     * "Actions" dropdown so the header stays clean.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
            Actions\Action::make('toggleActive')
                ->label(fn () => $this->record->is_active ? __('app.deactivate') : __('app.activate'))
                ->icon(fn () => $this->record->is_active ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn () => UserResource::canManage($this->record))
                ->action(function () {
                    UserResource::toggleActive($this->record);
                    $this->refreshFormData(['is_active']);
                }),

            Actions\Action::make('resetPassword')
                ->label(__('app.reset_password'))
                ->icon('heroicon-o-key')
                ->color('gray')
                ->visible(fn () => UserResource::canManage($this->record))
                ->modalHeading(fn () => __('app.reset_password') . ' — ' . $this->record->name)
                ->modalSubmitActionLabel(__('app.reset_password'))
                ->form(UserResource::passwordResetSchema())
                ->action(fn (array $data) => UserResource::resetPassword($this->record, $data)),

            Actions\Action::make('manageRoles')
                ->label(__('app.manage_roles'))
                ->icon('heroicon-o-shield-check')
                ->color('gray')
                ->visible(fn () => UserResource::canManage($this->record))
                ->fillForm(fn () => ['roles' => $this->record->roles->pluck('name')->all()])
                ->form(UserResource::rolesSchema())
                ->action(function (array $data) {
                    UserResource::syncRoles($this->record, $data);
                    $this->refreshFormData(['roles']);
                }),

            Actions\Action::make('disable2fa')
                ->label(__('app.force_disable_2fa'))
                ->icon('heroicon-o-shield-exclamation')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn () => UserResource::canManage($this->record) && UserResource::hasTwoFactor($this->record))
                ->action(function () {
                    UserResource::disable2fa($this->record);
                    $this->refreshFormData(['two_factor_confirmed_at', 'two_factor_enabled']);
                }),

            Actions\Action::make('requireTwoFactor')
                ->label(fn () => $this->record->two_factor_required
                    ? __('app.cancel_2fa_requirement')
                    : __('app.require_2fa_setup'))
                ->icon('heroicon-o-shield-check')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription(__('app.require_2fa_help'))
                ->visible(fn () => UserResource::canManage($this->record) && ! UserResource::hasTwoFactor($this->record))
                ->action(function () {
                    UserResource::setTwoFactorRequired($this->record, ! $this->record->two_factor_required);
                    $this->refreshFormData(['two_factor_required']);
                }),

            Actions\Action::make('unlock')
                ->label(__('app.unlock_account'))
                ->icon('heroicon-o-lock-open')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn () => UserResource::canManage($this->record) && UserResource::isLocked($this->record))
                ->action(function () {
                    UserResource::unlockAccount($this->record);
                }),

            Actions\Action::make('forceLogout')
                ->label(__('app.force_logout'))
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription(__('app.force_logout_help'))
                ->visible(fn () => UserResource::canManage($this->record))
                ->action(fn () => UserResource::forceLogout($this->record)),

            Actions\Action::make('activityTrail')
                ->label(__('app.activity_trail'))
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->url(fn () => UserResource::activityTrailUrl($this->record))
                ->visible(fn () => UserResource::activityTrailUrl($this->record) !== null),

            Actions\Action::make('viewProfile')
                ->label(__('app.view_linked_profile'))
                ->icon('heroicon-o-identification')
                ->color('gray')
                ->url(fn () => UserResource::profileUrl($this->record))
                ->visible(fn () => UserResource::profileUrl($this->record) !== null),
            ])
                ->label(__('app.manage_account'))
                ->icon('heroicon-o-cog-6-tooth')
                ->button(),
        ];
    }
}
