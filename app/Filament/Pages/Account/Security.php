<?php

namespace App\Filament\Pages\Account;

use App\Services\TwoFactorService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Security extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.account.security';

    protected static ?string $slug = 'account/security';

    protected static bool $shouldRegisterNavigation = false;

    /** Password form state. */
    public ?array $data = [];

    /** 2FA verification code (bound directly, outside the form). */
    public string $two_factor_code = '';

    public function getTitle(): string
    {
        return __('app.security');
    }

    public function mount(): void
    {
        $this->form->fill([]);
    }

    /*
    |--------------------------------------------------------------------------
    | Two-factor view helpers (used by the cohesive blade component)
    |--------------------------------------------------------------------------
    */
    public function isTwoFactorEnabled(): bool
    {
        return auth()->user()->two_factor_confirmed_at !== null;
    }

    public function getQrCodeHtml(): string
    {
        if ($this->isTwoFactorEnabled()) {
            return '';
        }

        $this->ensureTwoFactorSecret();

        return app(TwoFactorService::class)->qrCodeInline(auth()->user());
    }

    public function getSecretKey(): string
    {
        if ($this->isTwoFactorEnabled()) {
            return '';
        }

        $this->ensureTwoFactorSecret();

        return app(TwoFactorService::class)->getSecretKeyForSetup(auth()->user()) ?? '';
    }

    /**
     * Generate a pending 2FA secret (+ recovery codes) once, so the setup QR/key
     * can be shown before the user confirms. No-op if a secret already exists.
     */
    protected function ensureTwoFactorSecret(): void
    {
        $service = app(TwoFactorService::class);

        if (! $service->isEnabled(auth()->user())) {
            $service->enable(auth()->user());
        }
    }

    public function getRecoveryCodes(): array
    {
        $user = auth()->user();

        if (! $user->two_factor_recovery_codes) {
            return [];
        }

        try {
            return json_decode(decrypt($user->two_factor_recovery_codes), true) ?? [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Change password form
    |--------------------------------------------------------------------------
    */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.change_password'))
                    ->description(__('app.change_password_desc'))
                    ->icon('heroicon-o-key')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label(__('app.current_password'))
                            ->password()
                            ->revealable()
                            ->requiredWith('new_password')
                            ->currentPassword(),
                        Forms\Components\TextInput::make('new_password')
                            ->label(__('app.new_password'))
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->confirmed()
                            ->requiredWith('current_password'),
                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label(__('app.confirm_new_password'))
                            ->password()
                            ->revealable()
                            ->requiredWith('new_password'),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('save')
                                ->label(__('app.save_changes'))
                                ->icon('heroicon-m-check-circle')
                                ->color('primary')
                                ->action(fn () => $this->save()),
                        ]),
                    ])->columns(1),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [];
    }

    /** Filament confirmation modal for disabling 2FA. */
    public function disable2faAction(): Action
    {
        return Action::make('disable2fa')
            ->requiresConfirmation()
            ->color('danger')
            ->icon('heroicon-o-shield-exclamation')
            ->modalHeading(__('app.disable_2fa'))
            ->modalDescription(__('app.disable_2fa_confirm'))
            ->modalSubmitActionLabel(__('app.disable_2fa'))
            ->action(fn () => $this->disable2FA());
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        if (! empty($data['new_password'])) {
            $user->update([
                'password' => \Hash::make($data['new_password']),
            ]);

            $this->form->fill([]);

            Notification::make()
                ->title(__('app.password_updated'))
                ->success()
                ->send();
        }
    }

    public function enable2FA(): void
    {
        $user = auth()->user();
        $service = app(TwoFactorService::class);
        $code = trim($this->two_factor_code);

        if (empty($code)) {
            Notification::make()
                ->title(__('app.code_required'))
                ->body(__('app.enter_code_from_app'))
                ->danger()
                ->send();

            return;
        }

        if (! $service->isEnabled($user)) {
            $service->enable($user);
        }

        if (! $service->verify($user, $code)) {
            Notification::make()
                ->title(__('app.invalid_2fa_code'))
                ->body(__('app.invalid_2fa_code_body'))
                ->danger()
                ->send();

            return;
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->two_factor_code = '';

        Notification::make()
            ->title(__('app.2fa_enabled_title'))
            ->body(__('app.2fa_enabled_body'))
            ->success()
            ->send();
    }

    public function disable2FA(): void
    {
        auth()->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->two_factor_code = '';

        Notification::make()
            ->title(__('app.2fa_disabled_title'))
            ->body(__('app.2fa_disabled_body'))
            ->warning()
            ->send();
    }
}
