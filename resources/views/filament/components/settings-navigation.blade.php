@php
    $currentRoute = request()->route()->getName();
    $tabs = [
        [
            'name' => __('app.system'),
            'url' => \App\Filament\Pages\Settings\System::getUrl(),
            'icon' => 'heroicon-m-cog-6-tooth',
            'active' => str_contains($currentRoute, 'settings.system')
        ],
        [
            'name' => __('app.security'),
            'url' => \App\Filament\Pages\Settings\Security::getUrl(),
            'icon' => 'heroicon-m-shield-check',
            'active' => str_contains($currentRoute, 'settings.security')
        ],
        [
            'name' => __('app.academic'),
            'url' => \App\Filament\Pages\Settings\Academic::getUrl(),
            'icon' => 'heroicon-m-academic-cap',
            'active' => str_contains($currentRoute, 'settings.academic')
        ],
        [
            'name' => __('app.application'),
            'url' => \App\Filament\Pages\Settings\Application::getUrl(),
            'icon' => 'heroicon-m-computer-desktop', 
            'active' => str_contains($currentRoute, 'settings.application')
        ],
        [
            'name' => __('app.notifications'),
            'url' => \App\Filament\Pages\Settings\NotificationSettings::getUrl(),
            'icon' => 'heroicon-m-bell',
            'active' => str_contains($currentRoute, 'settings.notifications')
        ],
        [
            'name' => __('app.modules'),
            'url' => \App\Filament\Pages\Settings\Modules::getUrl(),
            'icon' => 'heroicon-m-puzzle-piece',
            'active' => str_contains($currentRoute, 'settings.modules')
        ]
    ];
@endphp

@include('filament.components.pill-navigation', ['tabs' => $tabs])