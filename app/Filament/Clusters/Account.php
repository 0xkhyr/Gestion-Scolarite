<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;

class Account extends Cluster
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?int $navigationSort = 9999;

    protected static ?string $slug = 'account';

    // Render the cluster's pages as a horizontal tab bar (replaces the old
    // hand-rolled pill navigation).
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('app.my_account');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('app.my_account');
    }
}
