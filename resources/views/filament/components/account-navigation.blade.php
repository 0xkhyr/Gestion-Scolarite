@php
    $currentRoute = request()->route()->getName();
    $tabs = [
        [
            'name' => __('app.profile'),
            'url' => \App\Filament\Pages\Account\Profile::getUrl(),
            'icon' => 'heroicon-m-user',
            'active' => str_contains($currentRoute, 'account.profile')
        ],
        [
            'name' => __('app.security'),
            'url' => \App\Filament\Pages\Account\Security::getUrl(),
            'icon' => 'heroicon-m-shield-check',
            'active' => str_contains($currentRoute, 'account.security')
        ],
        [
            'name' => __('app.preferences'),
            'url' => \App\Filament\Pages\Account\Preferences::getUrl(),
            'icon' => 'heroicon-m-cog-6-tooth',
            'active' => str_contains($currentRoute, 'account.preferences')
        ],
        [
            'name' => __('app.notifications'),
            'url' => \App\Filament\Pages\Account\Notifications::getUrl(),
            'icon' => 'heroicon-m-bell', 
            'active' => str_contains($currentRoute, 'account.notifications')
        ]
    ];
@endphp

@include('filament.components.pill-navigation', ['tabs' => $tabs])