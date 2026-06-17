<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use App\Services\SettingsService;
use App\Support\NotificationKeys;

class NotificationSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    
    protected static string $view = 'filament.pages.settings.notification-settings';
    

    public function getTitle(): string
    {
        return __('app.notification_settings');
    }
    
    protected static ?string $slug = 'settings/notifications';
    
    protected static bool $shouldRegisterNavigation = false;

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
        $this->form->fill([
            // Academic Notifications
            'enable_grade_published' => $this->settingsService->get('notifications.grade_published.enabled', true),
            'enable_evaluation_created' => $this->settingsService->get('notifications.evaluation_created.enabled', true),
            
            // Financial Notifications
            'enable_teacher_payment' => $this->settingsService->get('notifications.teacher_payment.enabled', true),
            'enable_student_payment' => $this->settingsService->get('notifications.student_payment.enabled', true),
            
            // Security Notifications
            'enable_lockout' => $this->settingsService->get('notifications.lockout.enabled', true),
            'enable_security_alert' => $this->settingsService->get('notifications.security_alert.enabled', true),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.academic_notifications'))
                    ->description(__('app.academic_notifications_desc'))
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\Toggle::make('enable_grade_published')
                            ->label(__('app.grade_published_notifications'))
                            ->helperText(__('app.grade_published_notifications_help'))
                            ->inline(false),

                        Forms\Components\Toggle::make('enable_evaluation_created')
                            ->label(__('app.evaluation_created_notifications'))
                            ->helperText(__('app.evaluation_created_notifications_help'))
                            ->inline(false),
                    ]),

                Forms\Components\Section::make(__('app.financial_notifications'))
                    ->description(__('app.financial_notifications_desc'))
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\Toggle::make('enable_teacher_payment')
                            ->label(__('app.teacher_payment_notifications'))
                            ->helperText(__('app.teacher_payment_notifications_help'))
                            ->inline(false),

                        Forms\Components\Toggle::make('enable_student_payment')
                            ->label(__('app.student_payment_notifications'))
                            ->helperText(__('app.student_payment_notifications_help'))
                            ->inline(false),
                    ]),

                Forms\Components\Section::make(__('app.security_notifications'))
                    ->description(__('app.security_notifications_desc'))
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Forms\Components\Toggle::make('enable_lockout')
                            ->label(__('app.account_lockout_notifications'))
                            ->helperText(__('app.account_lockout_notifications_help'))
                            ->inline(false),

                        Forms\Components\Toggle::make('enable_security_alert')
                            ->label(__('app.security_alert_notifications'))
                            ->helperText(__('app.security_alert_notifications_help'))
                            ->inline(false),
                    ]),

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
        
        // Save all notification settings
        $this->settingsService->set('notifications.grade_published.enabled', $data['enable_grade_published']);
        $this->settingsService->set('notifications.evaluation_created.enabled', $data['enable_evaluation_created']);
        $this->settingsService->set('notifications.teacher_payment.enabled', $data['enable_teacher_payment']);
        $this->settingsService->set('notifications.student_payment.enabled', $data['enable_student_payment']);
        $this->settingsService->set('notifications.lockout.enabled', $data['enable_lockout']);
        $this->settingsService->set('notifications.security_alert.enabled', $data['enable_security_alert']);
        
        Notification::make()
            ->title(__('app.notification_settings_saved'))
            ->success()
            ->send();
    }
}
