<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;

class Settings extends Cluster
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 100;

    protected static ?string $slug = 'settings';

    // Native horizontal tab bar across the settings pages (replaces the old
    // hand-rolled settings-navigation pills).
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('app.settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.systeme');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('app.settings');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin')
            || auth()->user()?->hasPermissionTo('setting.view');
    }
}
