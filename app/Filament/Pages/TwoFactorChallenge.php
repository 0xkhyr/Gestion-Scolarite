<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use App\Services\TwoFactorService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\RateLimiter;

class TwoFactorChallenge extends Page
{
    protected string $view = 'filament.pages.two-factor-challenge';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $layout = 'filament-panels::components.layout.simple';

    protected ?string $maxWidth = '2xl';

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
        // Guard: only for authenticated users with 2FA enabled
        if (! auth()->check()) {
            // Redirect to Filament login if not authenticated
            redirect()->route('filament.admin.auth.login');
            return;
        }

        $user = auth()->user();

        if (! $user?->two_factor_confirmed_at) {
            // If 2FA not enabled, send to setup
            redirect()->route('filament.admin.pages.two-factor-setup');
            return;
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

    protected function getFormActions(): array
    {
        return [
            Action::make('verify')
                ->label(__('app.verify'))
                ->action('verify'),
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('logout')
                ->label(__('filament-panels::pages/auth/login.actions.logout.label'))
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('gray')
                ->action(function () {
                    auth()->logout();
                    session()->invalidate();
                    session()->regenerateToken();

                    return redirect()->route('filament.admin.auth.login');
                }),
        ];
    }

    protected function rateLimitKey($user): string
    {
        return sprintf('2fa:challenge:%s:%s', $user->id, request()->ip());
    }

    public function verify()
    {
        // Ensure authentication + 2FA enabled at moment of verify
        if (! auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $user = auth()->user();
        if (! $user?->two_factor_confirmed_at) {
            return redirect()->route('filament.admin.pages.two-factor-setup');
        }

        $this->validate([
            'code' => 'required|string',
        ]);

        $key = $this->rateLimitKey($user);
        $maxAttempts = (int) setting('security.max_login_attempts', 5);
        $lockoutDuration = (int) setting('security.lockout_duration', 15); // minutes
        $decaySeconds = $lockoutDuration * 60; // Convert minutes to seconds

        // If rate limited, do not attempt verification
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $availableIn = RateLimiter::availableIn($key);

            logger()->warning('2FA rate limit lockout', [
                'user_id' => $user->id,
                'ip' => request()->ip(),
                'available_in' => $availableIn,
            ]);

            Notification::make()
                ->danger()
                ->title(__('app.too_many_attempts'))
                ->body(__('app.try_again_later'))
                ->send();

            return;
        }

        $twoFactorService = app(TwoFactorService::class);

        // Normalize & strict validation
        if ($this->useRecoveryCode) {
            $code = trim((string) $this->code);

            if ($code === '') {
                RateLimiter::hit($key, $decaySeconds);

                logger()->warning('2FA recovery code blank', ['user_id' => $user->id, 'ip' => request()->ip()]);

                Notification::make()
                    ->danger()
                    ->title(__('app.invalid_code'))
                    ->body(__('app.enter_recovery_code'))
                    ->send();

                return;
            }

            $verified = $twoFactorService->verifyRecoveryCode($user, $code);

            if (! $verified) {
                RateLimiter::hit($key, $decaySeconds);

                logger()->warning('2FA recovery code failed', ['user_id' => $user->id, 'ip' => request()->ip()]);

                Notification::make()
                    ->danger()
                    ->title(__('app.invalid_code'))
                    ->body(__('app.check_recovery_code'))
                    ->send();

                return;
            }

            // Success with recovery code
            session()->regenerate(); // prevent session fixation
            $twoFactorService->markAsVerified($user);
            RateLimiter::clear($key);

            logger()->info('2FA verified (recovery code)', ['user_id' => $user->id, 'ip' => request()->ip()]);

            // Inform user that a recovery code was used
            Notification::make()
                ->warning()
                ->title(__('app.recovery_code_used') ?: 'Recovery code utilisé, pensez à régénérer')
                ->send();

            return redirect()->intended(route('filament.admin.pages.dashboard'));
        }

        // OTP path (TOTP)
        $otp = preg_replace('/\s+/', '', (string) $this->code);

        if (! preg_match('/^\d{6}$/', $otp)) {
            RateLimiter::hit($key, $decaySeconds);

            logger()->warning('2FA invalid otp format', ['user_id' => $user->id, 'ip' => request()->ip(), 'value' => $this->code]);

            Notification::make()
                ->danger()
                ->title(__('app.invalid_code'))
                ->body(__('app.invalid_otp_format') ?: __('app.enter_6_digit_code'))
                ->send();

            return;
        }

        $verified = $twoFactorService->verify($user, $otp);

        if (! $verified) {
            RateLimiter::hit($key, $decaySeconds);

            logger()->warning('2FA otp failed', ['user_id' => $user->id, 'ip' => request()->ip()]);

            Notification::make()
                ->danger()
                ->title(__('app.invalid_code'))
                ->body(__('app.check_your_app'))
                ->send();

            return;
        }

        // Success
        session()->regenerate(); // anti session fixation
        $twoFactorService->markAsVerified($user);
        RateLimiter::clear($key);

        logger()->info('2FA verified (otp)', ['user_id' => $user->id, 'ip' => request()->ip()]);

        Notification::make()
            ->success()
            ->title(__('app.verification_successful'))
            ->send();

        return redirect()->intended(route('filament.admin.pages.dashboard'));
    }
}
