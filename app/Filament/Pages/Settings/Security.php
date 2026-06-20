<?php

namespace App\Filament\Pages\Settings;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Actions;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\SettingsService;

class Security extends Page
{
    protected static ?string $cluster = \App\Filament\Clusters\Settings::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('app.security_settings');
    }

    
    protected string $view = 'filament.pages.settings.security';
    
    protected static ?string $slug = 'security';

    public function getTitle(): string
    {
        return __('app.security_settings');
    }
    
    protected static bool $shouldRegisterNavigation = true;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') || auth()->user()?->hasPermissionTo('setting.manage');
    }

    public ?array $data = [];

    protected SettingsService $settingsService;

    public function boot(SettingsService $settingsService): void
    {
        $this->settingsService = $settingsService;
    }

    public function mount(): void
    {
        $securitySettings = $this->settingsService->getSecuritySettings();
        
        $this->form->fill([
            'two_factor_required' => $securitySettings['security.two_factor_required'],
            'session_timeout' => $securitySettings['security.session_timeout'],
            'password_min_length' => $securitySettings['security.password_min_length'],
            'password_require_uppercase' => $securitySettings['security.password_require_uppercase'],
            'password_require_lowercase' => $securitySettings['security.password_require_lowercase'],
            'password_require_numbers' => $securitySettings['security.password_require_numbers'],
            'password_require_symbols' => $securitySettings['security.password_require_symbols'],
            'max_login_attempts' => $securitySettings['security.max_login_attempts'],
            'lockout_duration' => $securitySettings['security.lockout_duration'],
            'password_expiry_enabled' => $securitySettings['security.password_expiry_enabled'],
            'password_expiry_days' => $securitySettings['security.password_expiry_days'],
            'force_https' => $securitySettings['security.force_https'],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.authentication_policies'))
                    ->icon('heroicon-o-finger-print')
                    ->description(__('app.authentication_policies_desc'))
                    ->schema([
                        Toggle::make('two_factor_required')
                            ->label(__('app.two_factor_required'))
                            ->helperText(__('app.two_factor_required_helper')),
                        Select::make('session_timeout')
                            ->label(__('app.session_timeout'))
                            ->options([
                                '15' => __('app.session_timeout_15'),
                                '30' => __('app.session_timeout_30'),
                                '60' => __('app.session_timeout_60'),
                                '120' => __('app.session_timeout_120'),
                                '240' => __('app.session_timeout_240'),
                                '480' => __('app.session_timeout_480'),
                            ])
                            ->required(),
                        Toggle::make('force_https')
                            ->label(__('app.force_https'))
                            ->helperText(__('app.force_https_helper')),
                    ])->columns(2),
                
                Section::make(__('app.password_policies'))
                    ->icon('heroicon-o-key')
                    ->description(__('app.password_policies_desc'))
                    ->schema([
                        TextInput::make('password_min_length')
                            ->label(__('app.password_min_length'))
                            ->integer()
                            ->minValue(6)
                            ->maxValue(50)
                            ->required(),
                        Toggle::make('password_require_uppercase')
                            ->label(__('app.password_require_uppercase'))
                            ->helperText(__('app.password_require_uppercase_helper')),
                        Toggle::make('password_require_lowercase')
                            ->label(__('app.password_require_lowercase'))
                            ->helperText(__('app.password_require_lowercase_helper')),
                        Toggle::make('password_require_numbers')
                            ->label(__('app.password_require_numbers'))
                            ->helperText(__('app.password_require_numbers_helper')),
                        Toggle::make('password_require_symbols')
                            ->label(__('app.password_require_symbols'))
                            ->helperText(__('app.password_require_symbols_helper')),
                        Toggle::make('password_expiry_enabled')
                            ->label(__('app.password_expiry_enabled'))
                            ->helperText(__('app.password_expiry_enabled_helper'))
                            ->live(),
                        TextInput::make('password_expiry_days')
                            ->label(__('app.password_expiry_days'))
                            ->integer()
                            ->minValue(1)
                            ->maxValue(365)
                            // Child of the toggle: only relevant when expiry is enabled.
                            ->visible(fn (Get $get) => (bool) $get('password_expiry_enabled'))
                            ->required(fn (Get $get) => (bool) $get('password_expiry_enabled'))
                            ->helperText(__('app.password_expiry_days_helper')),
                    ])->columns(2),
                
                Section::make(__('app.account_security'))
                    ->icon('heroicon-o-lock-closed')
                    ->description(__('app.account_security_desc'))
                    ->schema([
                        TextInput::make('max_login_attempts')
                            ->label(__('app.max_login_attempts'))
                            ->integer()
                            ->minValue(3)
                            ->maxValue(10)
                            ->required(),
                        TextInput::make('lockout_duration')
                            ->label(__('app.lockout_duration'))
                            ->integer()
                            ->minValue(1)
                            ->maxValue(1440)
                            ->required(),
                    ])->columns(2),
                
                Actions::make([
                    Action::make('save')
                        ->label(__('app.save_security_settings'))
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

        $securityData = [
            'security.two_factor_required' => $data['two_factor_required'],
            'security.session_timeout' => (int) $data['session_timeout'],
            'security.password_min_length' => (int) $data['password_min_length'],
            'security.password_require_uppercase' => $data['password_require_uppercase'],
            'security.password_require_lowercase' => $data['password_require_lowercase'],
            'security.password_require_numbers' => $data['password_require_numbers'],
            'security.password_require_symbols' => $data['password_require_symbols'],
            'security.max_login_attempts' => (int) $data['max_login_attempts'],
            'security.lockout_duration' => (int) $data['lockout_duration'],
            'security.password_expiry_enabled' => (bool) ($data['password_expiry_enabled'] ?? false),
            'security.password_expiry_days' => (int) ($data['password_expiry_days'] ?? 90),
            'security.force_https' => $data['force_https'],
        ];

        $this->settingsService->updateSecuritySettings($securityData);

        Notification::make()
            ->title(__('app.security_settings_saved'))
            ->success()
            ->send();
    }
}