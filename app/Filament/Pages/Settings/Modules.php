<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use App\Services\SettingsService;

class Modules extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.settings.modules';

    public function getTitle(): string
    {
        return __('app.modules_settings');
    }

    protected static ?string $slug = 'settings/modules';

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
        $moduleSettings = $this->settingsService->getModuleSettings();

        $this->form->fill([
            'attendance' => (bool) $moduleSettings['features.attendance'],
            'submissions' => (bool) $moduleSettings['features.submissions'],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.optional_modules'))
                    ->icon('heroicon-o-puzzle-piece')
                    ->description(__('app.optional_modules_desc'))
                    ->schema([
                        Forms\Components\Toggle::make('attendance')
                            ->label(__('app.module_attendance'))
                            ->helperText(__('app.module_attendance_help')),
                        Forms\Components\Toggle::make('submissions')
                            ->label(__('app.module_submissions'))
                            ->helperText(__('app.module_submissions_help')),
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

        $this->settingsService->updateModuleSettings([
            'features.attendance' => (bool) ($data['attendance'] ?? false),
            'features.submissions' => (bool) ($data['submissions'] ?? false),
        ]);

        Notification::make()
            ->title(__('app.modules_settings_saved'))
            ->success()
            ->send();
    }
}
