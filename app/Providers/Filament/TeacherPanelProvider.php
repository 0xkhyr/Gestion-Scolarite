<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Account\Profile;
use App\Filament\Pages\Dashboard;
use App\Http\Middleware\EnsureTeacherRole;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\MenuItem;
use App\Filament\Pages\Account;
use Cmsmaxinc\FilamentErrorPages\FilamentErrorPagesPlugin;

class TeacherPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->navigationGroups([
                NavigationGroup::make(__('app.tableau_de_bord')),
                NavigationGroup::make(__('app.mes_classes')),
                NavigationGroup::make(__('app.gestion_academique')),
            ])
            ->id('teacher')
            ->path('teacher')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->plugins([
                FilamentErrorPagesPlugin::make(),
            ])
            ->login(Login::class)
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Blue,
                'info' => Color::Cyan,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'danger' => Color::Rose,
                'gray' => Color::Gray,
            ])
            ->userMenuItems([
            MenuItem::make()
                ->label(__('app.my_account'))
                ->url(fn (): string => Profile::getUrl())
                ->icon('heroicon-o-user-circle'),
            // ...
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Teacher specific widgets
            ])
            ->brandName(function () {
                $user = auth()->user();
                if (!$user) return setting('school.name', 'School Administration');
                
                return __('app.administration_panel');
            })->brandLogo(setting('school.logo', asset('images/logo.svg')))
            ->favicon(asset('images/favicon.png'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureTeacherRole::class,
            ])
            ->spa()
            ->font('Poppins')
            ->sidebarFullyCollapsibleOnDesktop();
    }
}