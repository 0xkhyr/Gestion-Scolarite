<?php

namespace App\Providers\Filament;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\ServiceProvider;

class LanguageSwitchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en', 'fr'])
                ->labels([
                    'ar' => 'العربية',
                    'en' => 'English',
                    'fr' => 'Français',
                ]);
        });
    }
}
