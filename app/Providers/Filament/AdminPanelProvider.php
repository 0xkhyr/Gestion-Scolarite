<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Account\Profile;
use App\Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\EnsureTwoFactorIsVerified;
use App\Http\Middleware\ForcePasswordChange;
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


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->navigationGroups([
                NavigationGroup::make(__('app.gestion_academique')),
                NavigationGroup::make(__('app.personnes')),
                NavigationGroup::make(__('app.gestion_financiere')),
                NavigationGroup::make(__('app.systeme')),
                NavigationGroup::make(__('app.securite')),
            ])
            
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->plugins([
                FilamentErrorPagesPlugin::make(),
            ])
            ->login(Login::class)
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Amber,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'blue' => Color::Blue,
                'pink' => Color::Pink,
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
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,

            ])
            ->bootUsing(function () {
                // Register a global loading overlay in the Filament panel body end
                FilamentView::registerRenderHook(
                    PanelsRenderHook::BODY_END,
                    fn () => view('filament.loading-overlay')
                );
            })
            ->brandName(function () {
                $user = auth()->user();
                if (!$user) return setting('school.name', 'School Administration');
                
                return __('app.administration_panel');
            })->brandLogo(asset('images/logo.svg'))
            ->favicon(asset('images/favicon.png'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                // Sign deactivated accounts out gracefully (login redirect, not 403).
                EnsureUserIsActive::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureAdminRole::class,
                // No-op unless security.two_factor_required is enabled in settings.
                EnsureTwoFactorIsVerified::class,
                // No-op unless security.password_expiry_enabled is on.
                ForcePasswordChange::class,
            ])
            ->spa()
            ->font('Poppins')
            ->sidebarFullyCollapsibleOnDesktop();
    }
}
