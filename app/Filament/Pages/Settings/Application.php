<?php

namespace App\Filament\Pages\Settings;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\SettingsService;

class Application extends Page
{
    protected static ?string $cluster = \App\Filament\Clusters\Settings::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('app.application_settings');
    }

    
    protected string $view = 'filament.pages.settings.application';
    

    public function getTitle(): string
    {
        return __('app.application_settings');
    }
    
    protected static ?string $slug = 'application';
    
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
        $applicationSettings = $this->settingsService->getApplicationSettings();
        
        $this->form->fill([
            'app_name' => $applicationSettings['app.name'],
            'default_user_role' => $applicationSettings['app.default_user_role'],
            'registration_enabled' => $applicationSettings['app.registration_enabled'],
            'email_verification_required' => $applicationSettings['app.email_verification_required'],
            'notifications_enabled' => $applicationSettings['app.notifications_enabled'],
            'file_upload_max_size' => $applicationSettings['app.file_upload_max_size'],
            'backup_frequency' => $applicationSettings['app.backup_frequency'],
            'auto_backup_enabled' => $applicationSettings['app.auto_backup_enabled'],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.application_information'))
                    ->description(__('app.application_information_desc'))
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextInput::make('app_name')
                            ->label(__('app.application_name'))
                            ->required()
                            ->maxLength(255),
                        Select::make('default_user_role')
                            ->label(__('app.default_user_role'))
                            ->options([
                                'student' => __('app.student'),
                                'teacher' => __('app.teacher'),
                                'parent' => __('app.parent'),
                                'staff' => __('app.staff'),
                            ])
                            ->required(),
                    ])->columns(2),

                Section::make(__('app.user_registration_access'))
                    ->description(__('app.user_registration_access_desc'))
                    ->icon('heroicon-o-user-plus')
                    ->schema([
                        Toggle::make('registration_enabled')
                            ->label(__('app.allow_user_registration'))
                            ->helperText(__('app.allow_user_registration_help')),
                        Toggle::make('email_verification_required')
                            ->label(__('app.require_email_verification'))
                            ->helperText(__('app.require_email_verification_help')),
                        Toggle::make('notifications_enabled')
                            ->label(__('app.enable_system_notifications'))
                            ->helperText(__('app.enable_system_notifications_help')),
                    ])->columns(2),

                Section::make(__('app.file_management'))
                    ->description(__('app.file_management_desc'))
                    ->icon('heroicon-o-document-arrow-up')
                    ->schema([
                        TextInput::make('file_upload_max_size')
                            ->label(__('app.max_file_upload_size'))
                            ->integer()
                            ->minValue(1)
                            ->maxValue(100)
                            ->required(),
                    ])->columns(2),

                Section::make(__('app.system_backup'))
                    ->description(__('app.system_backup_desc'))
                    ->icon('heroicon-o-circle-stack')
                    ->schema([
                        Toggle::make('auto_backup_enabled')
                            ->label(__('app.enable_auto_backups'))
                            ->helperText(__('app.enable_auto_backups_help')),
                        Select::make('backup_frequency')
                            ->label(__('app.backup_frequency'))
                            ->options([
                                'daily' => __('app.daily'),
                                'weekly' => __('app.weekly'),
                                'monthly' => __('app.monthly'),
                            ])
                            ->required(),
                    ])->columns(2),

                Actions::make([
                    Action::make('save')
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

        $applicationData = [
            'app.name' => $data['app_name'],
            'app.default_user_role' => $data['default_user_role'],
            'app.registration_enabled' => $data['registration_enabled'],
            'app.email_verification_required' => $data['email_verification_required'],
            'app.notifications_enabled' => $data['notifications_enabled'],
            'app.file_upload_max_size' => (int) $data['file_upload_max_size'],
            'app.backup_frequency' => $data['backup_frequency'],
            'app.auto_backup_enabled' => $data['auto_backup_enabled'],
        ];

        $this->settingsService->updateApplicationSettings($applicationData);

        Notification::make()
            ->title(__('app.application_settings_saved'))
            ->success()
            ->send();
    }
}