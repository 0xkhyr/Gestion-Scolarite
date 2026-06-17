<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\SettingsService;

class System extends Page
{
    protected static ?string $navigationIcon = null;
    
    protected static string $view = 'filament.pages.settings.system';
    
    protected static ?string $slug = 'settings/system';

    public function getTitle(): string
    {
        return __('app.system_settings');
    }
    
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
        $organizationSettings = $this->settingsService->getSchoolSettings();
        $systemSettings = $this->settingsService->getSystemSettings();
        
        $this->form->fill([
            // Organization Settings
            'school_name' => $organizationSettings['school.name'],
            'school_address' => $organizationSettings['school.address'],
            'school_phone' => $organizationSettings['school.phone'],
            'school_email' => $organizationSettings['school.email'],
            'school_website' => $organizationSettings['school.website'],
            'school_location' => $organizationSettings['school.location'],
            'school_latitude' => $organizationSettings['school.latitude'],
            'school_longitude' => $organizationSettings['school.longitude'],
            'academic_year_start' => $organizationSettings['school.academic_year_start'],
            'academic_year_end' => $organizationSettings['school.academic_year_end'],
            
            // System Settings
            'timezone' => $systemSettings['system.timezone'],
            'date_format' => $systemSettings['system.date_format'],
            'language' => $systemSettings['system.language'],
            'currency' => $systemSettings['system.currency'],
            'items_per_page' => $systemSettings['system.items_per_page'],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.organization_information'))
                    ->description(__('app.organization_information_desc'))
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\TextInput::make('school_name')
                            ->label(__('app.school_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('school_address')
                            ->label(__('app.school_address'))
                            ->rows(3),
                        Forms\Components\TextInput::make('school_phone')
                            ->label(__('app.phone_number'))
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('school_email')
                            ->label(__('app.email'))
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('school_website')
                            ->label(__('app.website'))
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('school_location')
                            ->label(__('app.location_city'))
                            ->maxLength(255)
                            ->helperText(__('app.location_city_help')),
                    ])->columns(2),

                Forms\Components\Section::make(__('app.map_coordinates'))
                    ->description(__('app.map_coordinates_desc'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\TextInput::make('school_latitude')
                            ->label(__('app.latitude'))
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90)
                            ->step('any')
                            ->reactive()
                            ->helperText(__('app.coord_example_latitude')),
                        Forms\Components\TextInput::make('school_longitude')
                            ->label(__('app.longitude'))
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180)
                            ->step('any')
                            ->reactive()
                            ->helperText(__('app.coord_example_longitude')),
                        Forms\Components\Placeholder::make('coordinate_helper')
                            ->label('')
                            ->content(function () {
                                $jsLabels = json_encode([
                                    'unsupported' => __('app.geolocation_not_supported'),
                                    'getting' => __('app.getting_location'),
                                    'success' => __('app.location_set_successfully'),
                                    'error' => __('app.error_getting_location'),
                                    'denied' => __('app.location_access_denied'),
                                    'unavailable' => __('app.location_unavailable'),
                                    'timeout' => __('app.location_request_timed_out'),
                                ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG);
                                $quickSetup = e(__('app.quick_setup'));
                                $getLocation = e(__('app.get_current_location'));
                                $gettingLocation = e(__('app.getting_location'));
                                $manualCoords = e(__('app.manual_coordinates'));
                                $stepGoto = e(__('app.maps_step_goto'));
                                $stepFind = e(__('app.maps_step_find'));
                                $stepRightclick = e(__('app.maps_step_rightclick'));
                                $stepCopy = e(__('app.maps_step_copy'));

                                $xdata = <<<JS
{
    labels: {$jsLabels},
    status: '',
    statusClass: '',
    loading: false,
    getCurrentLocation() {
        if (!navigator.geolocation) {
            this.status = this.labels.unsupported;
            this.statusClass = 'text-red-600';
            return;
        }
        this.status = this.labels.getting;
        this.statusClass = 'text-blue-600';
        this.loading = true;
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude.toFixed(6);
                const lng = position.coords.longitude.toFixed(6);
                \$wire.set('data.school_latitude', lat);
                \$wire.set('data.school_longitude', lng);
                this.status = this.labels.success + ' (' + lat + ', ' + lng + ')';
                this.statusClass = 'text-green-600 font-medium';
                this.loading = false;
            },
            (error) => {
                let errorMessage = this.labels.error;
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = this.labels.denied;
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = this.labels.unavailable;
                        break;
                    case error.TIMEOUT:
                        errorMessage = this.labels.timeout;
                        break;
                }
                this.status = errorMessage;
                this.statusClass = 'text-red-600';
                this.loading = false;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }
}
JS;
                                $xdataAttr = htmlspecialchars($xdata, ENT_QUOTES);

                                $html = <<<HTML
<div x-data="{$xdataAttr}">
<div class="text-sm text-gray-600 mb-4 dark:text-gray-400">
<p class="mb-2"><strong>{$quickSetup}:</strong></p>
<button type="button" @click="getCurrentLocation()" :disabled="loading" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
<svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
<span x-show="!loading">{$getLocation}</span>
<span x-show="loading">{$gettingLocation}</span>
</button>
<span x-text="status" :class="statusClass" class="ms-3 text-sm"></span>
</div>
<div class="text-sm text-gray-600 mt-4 pt-4 border-t border-gray-200 dark:text-gray-400 dark:border-white/10">
<p class="mb-2"><strong>{$manualCoords}:</strong></p>
<ol class="list-decimal list-inside space-y-1">
<li>{$stepGoto} <a href="https://www.google.com/maps" target="_blank" class="text-primary-600 hover:underline">Google Maps</a></li>
<li>{$stepFind}</li>
<li>{$stepRightclick}</li>
<li>{$stepCopy}</li>
</ol>
</div>
</div>
HTML;

                                return new \Illuminate\Support\HtmlString($html);
                            }),
                    ])->columns(2),
                
                Forms\Components\Section::make(__('app.academic_year'))
                    ->description(__('app.academic_year_desc'))
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Forms\Components\TextInput::make('academic_year_start')
                            ->label(__('app.academic_year_start'))
                            ->placeholder('09-01')
                            ->helperText(__('app.academic_year_start_help'))
                            ->maxLength(5),
                        Forms\Components\TextInput::make('academic_year_end')
                            ->label(__('app.academic_year_end'))
                            ->placeholder('06-30')
                            ->helperText(__('app.academic_year_end_help'))
                            ->maxLength(5),
                    ])->columns(2),

                Forms\Components\Section::make(__('app.regional_settings'))
                    ->description(__('app.regional_settings_desc'))
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Forms\Components\Select::make('timezone')
                            ->label(__('app.default_timezone'))
                            ->options([
                                'UTC' => 'UTC (UTC+0)',
                                'Africa/Casablanca' => 'Africa/Casablanca (UTC+1)',
                                'Africa/Tunis' => 'Africa/Tunis (UTC+1)',
                                'Africa/Algiers' => 'Africa/Algiers (UTC+1)',
                                'Europe/Paris' => 'Europe/Paris (UTC+1)',
                                'America/New_York' => 'America/New_York (UTC-5)',
                                'America/Los_Angeles' => 'America/Los_Angeles (UTC-8)',
                            ])
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('language')
                            ->label(__('app.default_language'))
                            ->options([
                                'en' => 'English',
                                'fr' => 'Français',
                                'ar' => 'العربية',
                                'es' => 'Español',
                            ])
                            ->required(),
                        Forms\Components\Select::make('currency')
                            ->label(__('app.default_currency'))
                            ->options([
                                'USD' => 'US Dollar (USD)',
                                'EUR' => 'Euro (EUR)',
                                'MAD' => 'Moroccan Dirham (MAD)',
                                'TND' => 'Tunisian Dinar (TND)',
                                'DZD' => 'Algerian Dinar (DZD)',
                            ])
                            ->required(),
                        Forms\Components\Select::make('date_format')
                            ->label(__('app.date_format'))
                            ->options([
                                'Y-m-d' => 'YYYY-MM-DD (2024-12-31)',
                                'd/m/Y' => 'DD/MM/YYYY (31/12/2024)',
                                'm/d/Y' => 'MM/DD/YYYY (12/31/2024)',
                                'd-m-Y' => 'DD-MM-YYYY (31-12-2024)',
                            ])
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make(__('app.system_preferences'))
                    ->description(__('app.system_preferences_desc'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\Select::make('items_per_page')
                            ->label(__('app.items_per_page'))
                            ->options([
                                '10' => '10',
                                '25' => '25',
                                '50' => '50',
                                '100' => '100',
                            ])
                            ->required(),
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

        // Save organization settings
        $organizationData = [
            'school.name' => $data['school_name'],
            'school.address' => $data['school_address'],
            'school.phone' => $data['school_phone'],
            'school.email' => $data['school_email'],
            'school.website' => $data['school_website'],
            'school.location' => $data['school_location'],
            'school.latitude' => $data['school_latitude'] ?? '',
            'school.longitude' => $data['school_longitude'] ?? '',
            'school.academic_year_start' => $data['academic_year_start'],
            'school.academic_year_end' => $data['academic_year_end'],
        ];
        $this->settingsService->updateSchoolSettings($organizationData);

        // Save system settings
        $systemData = [
            'system.timezone' => $data['timezone'],
            'system.date_format' => $data['date_format'],
            'system.language' => $data['language'],
            'system.currency' => $data['currency'],
            'system.items_per_page' => (int) $data['items_per_page'],
        ];
        $this->settingsService->updateSystemSettings($systemData);

        Notification::make()
            ->title(__('app.system_settings_saved'))
            ->success()
            ->send();
    }
}