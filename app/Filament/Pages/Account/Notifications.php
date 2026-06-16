<?php

namespace App\Filament\Pages\Account;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\NotificationPreference;
use App\Support\NotificationKeys;
use App\Support\NotificationChannels;
use Illuminate\Support\Facades\DB;

class Notifications extends Page
{
    protected static ?string $navigationIcon = null;
    
    protected static string $view = 'filament.pages.account.notifications';
    
    protected static ?string $slug = 'account/notifications';

    public function getTitle(): string
    {
        return __('app.notification_settings');
    }
    
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        
        // Load existing preferences or default to true
        $preferences = NotificationPreference::where('user_id', $user->id)
            ->get()
            ->groupBy('key');

        $this->form->fill([
            // Login Notifications
            'login_attempt_mail' => $this->getPreference($preferences, NotificationKeys::LOGIN_ATTEMPT, NotificationChannels::MAIL),
            'login_attempt_database' => $this->getPreference($preferences, NotificationKeys::LOGIN_ATTEMPT, NotificationChannels::DATABASE),
            
            // Security Alerts
            'security_alert_mail' => $this->getPreference($preferences, NotificationKeys::SECURITY_ALERT, NotificationChannels::MAIL),
            'security_alert_database' => $this->getPreference($preferences, NotificationKeys::SECURITY_ALERT, NotificationChannels::DATABASE),
            
            // System Updates
            'system_update_mail' => $this->getPreference($preferences, NotificationKeys::SYSTEM_UPDATE, NotificationChannels::MAIL),
            'system_update_database' => $this->getPreference($preferences, NotificationKeys::SYSTEM_UPDATE, NotificationChannels::DATABASE),
            
            // Academic Updates (Grades)
            'grade_published_mail' => $this->getPreference($preferences, NotificationKeys::GRADE_PUBLISHED, NotificationChannels::MAIL),
            'grade_published_database' => $this->getPreference($preferences, NotificationKeys::GRADE_PUBLISHED, NotificationChannels::DATABASE),
        ]);
    }

    protected function getPreference($preferences, $key, $channel): bool
    {
        if (isset($preferences[$key])) {
            $pref = $preferences[$key]->firstWhere('channel', $channel);
            return $pref ? $pref->enabled : true; // Default to true if not found
        }
        return true;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.security_notifications'))
                    ->description(__('app.account_security_notifications_desc'))
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Placeholder::make('label_login')
                                    ->label(__('app.login_attempts'))
                                    ->content(__('app.login_attempts_desc')),
                                Forms\Components\Toggle::make('login_attempt_mail')
                                    ->label(__('app.email')),
                                Forms\Components\Toggle::make('login_attempt_database')
                                    ->label(__('app.in_app')),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Placeholder::make('label_security')
                                    ->label(__('app.security_alerts'))
                                    ->content(__('app.security_alerts_desc')),
                                Forms\Components\Toggle::make('security_alert_mail')
                                    ->label(__('app.email')),
                                Forms\Components\Toggle::make('security_alert_database')
                                    ->label(__('app.in_app')),
                            ]),
                    ]),

                Forms\Components\Section::make(__('app.system_notifications'))
                    ->description(__('app.system_notifications_account_desc'))
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Placeholder::make('label_system')
                                    ->label(__('app.system_updates'))
                                    ->content(__('app.system_updates_desc')),
                                Forms\Components\Toggle::make('system_update_mail')
                                    ->label(__('app.email')),
                                Forms\Components\Toggle::make('system_update_database')
                                    ->label(__('app.in_app')),
                            ]),
                    ]),

                Forms\Components\Section::make(__('app.academic_notifications'))
                    ->description(__('app.academic_notifications_account_desc'))
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Placeholder::make('label_grades')
                                    ->label(__('app.grade_published'))
                                    ->content(__('app.grade_published_desc')),
                                Forms\Components\Toggle::make('grade_published_mail')
                                    ->label(__('app.email')),
                                Forms\Components\Toggle::make('grade_published_database')
                                    ->label(__('app.in_app')),
                            ]),
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
        $user = auth()->user();

        DB::transaction(function () use ($user, $data) {
            $this->savePreference($user, NotificationKeys::LOGIN_ATTEMPT, NotificationChannels::MAIL, $data['login_attempt_mail']);
            $this->savePreference($user, NotificationKeys::LOGIN_ATTEMPT, NotificationChannels::DATABASE, $data['login_attempt_database']);
            
            $this->savePreference($user, NotificationKeys::SECURITY_ALERT, NotificationChannels::MAIL, $data['security_alert_mail']);
            $this->savePreference($user, NotificationKeys::SECURITY_ALERT, NotificationChannels::DATABASE, $data['security_alert_database']);
            
            $this->savePreference($user, NotificationKeys::SYSTEM_UPDATE, NotificationChannels::MAIL, $data['system_update_mail']);
            $this->savePreference($user, NotificationKeys::SYSTEM_UPDATE, NotificationChannels::DATABASE, $data['system_update_database']);
            
            $this->savePreference($user, NotificationKeys::GRADE_PUBLISHED, NotificationChannels::MAIL, $data['grade_published_mail']);
            $this->savePreference($user, NotificationKeys::GRADE_PUBLISHED, NotificationChannels::DATABASE, $data['grade_published_database']);
        });

        Notification::make()
            ->title(__('app.notification_preferences_saved'))
            ->success()
            ->send();
    }

    protected function savePreference($user, $key, $channel, $enabled): void
    {
        NotificationPreference::updateOrCreate(
            [
                'user_id' => $user->id,
                'key' => $key,
                'channel' => $channel,
            ],
            [
                'enabled' => $enabled
            ]
        );
    }
}