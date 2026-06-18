<?php

namespace App\Filament\Auth;

use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use Exception;
use App\Services\TwoFactorService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;

class TwoFactorChallenge extends SimplePage
{
    protected string $view = 'filament.pages.two-factor-challenge';
    
    protected static string $routePath = 'two-factor-challenge';
    
    protected Width|string|null $maxWidth = '2xl';

    public ?string $code = null;
    public bool $useRecoveryCode = false;

    public function getTitle(): string
    {
        return __('app.two_factor_challenge');
    }

    public function getHeading(): string
    {
        return __('app.verify_identity');
    }

    public function mount(): void
    {
        if (!auth()->user()->two_factor_confirmed_at) {
            redirect()->route('filament.admin.pages.two-factor-setup');
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('useRecoveryCode')
                    ->label(__('app.use_recovery_code'))
                    ->helperText(__('app.recovery_code_helper'))
                    ->live(),

                TextInput::make('code')
                    ->label(fn () => $this->useRecoveryCode 
                        ? __('app.recovery_code') 
                        : __('app.authentication_code'))
                    ->placeholder(fn () => $this->useRecoveryCode ? 'XXXX-XXXX' : '000000')
                    ->required()
                    ->helperText(fn () => $this->useRecoveryCode 
                        ? __('app.enter_recovery_code')
                        : __('app.enter_6_digit_code')),
            ]);
    }

    public function verify(): void
    {
        $this->validate([
            'code' => 'required|string',
        ]);

        $twoFactorService = app(TwoFactorService::class);
        $user = auth()->user();

        try {
            $twoFactorService->ensureNotRateLimited($user);
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('app.too_many_attempts'))
                ->body(__('app.try_again_later'))
                ->send();
            return;
        }

        $verified = $this->useRecoveryCode
            ? $twoFactorService->verifyRecoveryCode($user, $this->code)
            : $twoFactorService->verify($user, $this->code);

        if (!$verified) {
            $twoFactorService->incrementFailedAttempts($user);

            Notification::make()
                ->danger()
                ->title(__('app.invalid_code'))
                ->body(__('app.check_your_app'))
                ->send();
            return;
        }

        $twoFactorService->clearFailedAttempts($user);
        $twoFactorService->markAsVerified($user);

        $intendedUrl = session()->pull('2fa_intended_url', route('filament.admin.pages.dashboard'));

        Notification::make()
            ->success()
            ->title(__('app.verification_successful'))
            ->send();

        redirect($intendedUrl);
    }
    
    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
