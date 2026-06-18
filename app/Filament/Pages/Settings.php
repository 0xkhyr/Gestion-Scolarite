<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Settings\System;
use Filament\Pages\Page;

class Settings extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.settings';

    public function getTitle(): string
    {
        return __('app.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.systeme');
    }
    
    public static function getNavigationSort(): ?int
    {
        return 100;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') || auth()->user()?->hasPermissionTo('setting.view');
    }

    public function mount()
    {
        // Redirect to system settings page by default
        return redirect()->to(System::getUrl());
    }
}