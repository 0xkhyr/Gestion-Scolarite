<?php

namespace App\Filament\Pages\Account;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class Security extends Page
{
    protected static ?string $navigationIcon = null;
    
    protected static string $view = 'filament.pages.account.security';
    
    protected static ?string $slug = 'account/security';

    public function getTitle(): string
    {
        return __('app.security');
    }
    
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.two_factor_authentication'))
                    ->description(__('app.two_factor_authentication_desc'))
                    ->collapsible()
                    ->collapsed(fn () => auth()->user()->two_factor_confirmed_at !== null)
                    ->schema([
                        Forms\Components\Placeholder::make('two_factor_status')
                            ->label(__('app.2fa_status'))
                            ->content(function () {
                                $user = auth()->user();
                                $isEnabled = $user->two_factor_confirmed_at !== null;
                                
                                if ($isEnabled) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center gap-2">' .
                                        '<svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="currentColor" viewBox="0 0 20 20">' .
                                        '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>' .
                                        '</svg>' .
                                        '<span class="text-sm font-medium text-success-600 dark:text-success-400">' . __('app.enabled') . '</span>' .
                                        '</div>'
                                    );
                                } else {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center gap-2">' .
                                        '<svg class="w-5 h-5 text-danger-600 dark:text-danger-400" fill="currentColor" viewBox="0 0 20 20">' .
                                        '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>' .
                                        '</svg>' .
                                        '<span class="text-sm font-medium text-danger-600 dark:text-danger-400">' . __('app.disabled') . '</span>' .
                                        '</div>'
                                    );
                                }
                            }),
                        
                        // QR Code for setup
                        Forms\Components\Placeholder::make('qr_code')
                            ->label(__('app.qr_code'))
                            ->content(function () {
                                $user = auth()->user();
                                if ($user->two_factor_confirmed_at !== null) {
                                    return '';
                                }
                                
                                $service = app(\App\Services\TwoFactorService::class);
                                return new \Illuminate\Support\HtmlString(
                                    '<div class="p-4 border rounded-lg bg-gray-50">' .
                                    '<p class="text-sm text-gray-600 mb-3">' . __('app.scan_qr_help') . '</p>' .
                                    '<div class="flex justify-center">' .
                                    $service->qrCodeInline($user) .
                                    '</div>' .
                                    '</div>'
                                );
                            })
                            ->visible(fn () => auth()->user()->two_factor_confirmed_at === null),
                        
                        Forms\Components\Placeholder::make('secret_key')
                            ->label(__('app.secret_key'))
                            ->content(function () {
                                $user = auth()->user();
                                if ($user->two_factor_confirmed_at !== null) {
                                    return '';
                                }
                                
                                $service = app(\App\Services\TwoFactorService::class);
                                return new \Illuminate\Support\HtmlString(
                                    '<div class="p-3 bg-gray-50 border rounded-lg">' .
                                    '<p class="text-sm text-gray-600 mb-2">' . __('app.manual_entry_helper') . '</p>' .
                                    '<code class="bg-gray-100 px-2 py-1 rounded font-mono text-sm">' .
                                    e($service->getSecretKeyForSetup($user)) .
                                    '</code>' .
                                    '</div>'
                                );
                            })
                            ->visible(fn () => auth()->user()->two_factor_confirmed_at === null),
                        
                        Forms\Components\TextInput::make('two_factor_code')
                            ->label(__('app.code_2fa'))
                            ->helperText(__('app.enter_code_from_app'))
                            ->numeric()
                            ->length(6)
                            ->placeholder('123456')
                            ->visible(fn () => auth()->user()->two_factor_confirmed_at === null),
                        
                        // Recovery codes for enabled 2FA
                        Forms\Components\Placeholder::make('backup_codes_info')
                            ->label(__('app.recovery_codes'))
                            ->content(function () {
                                $user = auth()->user();
                                $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);
                                
                                return new \Illuminate\Support\HtmlString(
                                    '<div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">' .
                                    '<p class="text-sm text-amber-800 mb-2 font-medium">' . __('app.recovery_codes') . ':</p>' .
                                    '<p class="text-xs text-amber-700 mb-3">' . __('app.recovery_codes_helper') . '</p>' .
                                    '<div class="grid grid-cols-2 gap-2">' .
                                    implode('', array_map(function($code) {
                                        return '<code class="bg-white px-2 py-1 rounded border text-xs font-mono">' . e($code) . '</code>';
                                    }, $codes)) .
                                    '</div>' .
                                    '</div>'
                                );
                            })
                            ->visible(fn () => auth()->user()->two_factor_confirmed_at !== null),
                        
                        // Action Buttons
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('enable_2fa')
                                ->label(__('app.enable_2fa'))
                                ->icon('heroicon-m-shield-check')
                                ->color('success')
                                ->action(function () {
                                    $this->enable2FA();
                                })
                                ->visible(fn () => auth()->user()->two_factor_confirmed_at === null),
                            
                            Forms\Components\Actions\Action::make('disable_2fa')
                                ->label(__('app.disable_2fa'))
                                ->icon('heroicon-m-shield-exclamation')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->action(function () {
                                    $this->disable2FA();
                                })
                                ->visible(fn () => auth()->user()->two_factor_confirmed_at !== null),
                        ]),
                    ]),
                
                Forms\Components\Section::make(__('app.change_password'))
                    ->description(__('app.change_password_desc'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label(__('app.current_password'))
                            ->password()
                            ->requiredWith('new_password')
                            ->currentPassword(),
                        Forms\Components\TextInput::make('new_password')
                            ->label(__('app.new_password'))
                            ->password()
                            ->minLength(8)
                            ->confirmed()
                            ->requiredWith('current_password'),
                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label(__('app.confirm_new_password'))
                            ->password()
                            ->requiredWith('new_password'),
                    ])->columns(1),
                    Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('save')
                        ->label(__('app.save_changes'))
                        ->icon('heroicon-m-check-circle')
                        ->color('primary')
                        ->action(function () {
                            $this->save();
                        }),
                ])
                    
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        // Handle password change if provided
        if (!empty($data['new_password'])) {
            $user->update([
                'password' => \Hash::make($data['new_password'])
            ]);
            
            Notification::make()
                ->title(__('app.password_updated'))
                ->success()
                ->send();
        }
    }

    public function enable2FA(): void
    {
        $user = auth()->user();
        $service = app(\App\Services\TwoFactorService::class);

        $formState = $this->form->getState();
        $code = $formState['two_factor_code'] ?? null;

        if (empty($code)) {
            Notification::make()
                ->title(__('app.code_required'))
                ->body(__('app.enter_code_from_app'))
                ->danger()
                ->send();
            return;
        }

        if (!$service->isEnabled($user)) {
            $service->enable($user);
        }

        if (!$service->verify($user, $code)) {
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

        Notification::make()
            ->title(__('app.2fa_enabled_title'))
            ->body(__('app.2fa_enabled_body'))
            ->success()
            ->send();

        $this->form->fill($this->form->getState());
    }

    public function disable2FA(): void
    {
        $user = auth()->user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        Notification::make()
            ->title(__('app.2fa_disabled_title'))
            ->body(__('app.2fa_disabled_body'))
            ->warning()
            ->send();

        $this->form->fill($this->form->getState());
    }
}