<?php

namespace App\Filament\Pages\Account;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class Preferences extends Page
{
    protected static ?string $navigationIcon = null;
    
    protected static string $view = 'filament.pages.account.preferences';
    
    protected static ?string $slug = 'account/preferences';

    public function getTitle(): string
    {
        return __('app.preferences');
    }
    
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'preferred_language' => session('locale', 'fr'),
            'date_format' => 'Y-m-d',
            'time_format' => '24',
            'theme' => 'system',
            'sidebar_collapsed' => 'auto',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.language_region'))
                    ->description(__('app.language_region_desc'))
                    ->schema([
                        Forms\Components\Select::make('preferred_language')
                            ->label(__('app.preferred_language'))
                            ->options([
                                'fr' => 'Français',
                                'ar' => 'العربية',
                                'en' => 'English',
                            ])
                            ->default('fr'),
                        Forms\Components\Select::make('date_format')
                            ->label(__('app.date_format'))
                            ->options([
                                'Y-m-d' => '2026-02-03',
                                'd/m/Y' => '03/02/2026',
                                'm/d/Y' => '02/03/2026',
                                'd-m-Y' => '03-02-2026',
                            ])
                            ->default('Y-m-d'),
                        Forms\Components\Select::make('time_format')
                            ->label(__('app.time_format'))
                            ->options([
                                '24' => __('app.time_format_24'),
                                '12' => __('app.time_format_12'),
                            ])
                            ->default('24'),
                    ])->columns(2),
                
                Forms\Components\Section::make(__('app.interface_preferences'))
                    ->description(__('app.interface_preferences_desc'))
                    ->schema([
                        Forms\Components\Select::make('theme')
                            ->label(__('app.theme'))
                            ->options([
                                'system' => __('app.theme_system'),
                                'light' => __('app.theme_light'),
                                'dark' => __('app.theme_dark'),
                            ])
                            ->default('system'),
                        Forms\Components\Select::make('sidebar_collapsed')
                            ->label(__('app.sidebar_behavior'))
                            ->options([
                                'expanded' => __('app.sidebar_expanded'),
                                'collapsed' => __('app.sidebar_collapsed'),
                                'auto' => __('app.sidebar_auto'),
                            ])
                            ->default('auto'),
                    ])->columns(2),

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

        // Save language preference
        if ($data['preferred_language']) {
            session(['locale' => $data['preferred_language']]);
        }

        Notification::make()
            ->title(__('app.preferences_saved'))
            ->success()
            ->send();
    }
}