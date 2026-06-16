<?php

namespace App\Providers\Filament;

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

class StaffPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->navigationGroups([
                NavigationGroup::make(__('app.dashboard')),
                NavigationGroup::make(__('app.gestion_etudiants')),
                NavigationGroup::make(__('app.gestion_enseignants')),
                NavigationGroup::make(__('app.gestion_financiere')),
                NavigationGroup::make(__('app.rapports')),
            ])
            ->id('staff')
            ->path('staff')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Emerald,
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Orange,
                'danger' => Color::Rose,
                'gray' => Color::Gray,
            ])
            ->userMenuItems([
            MenuItem::make()
                ->label(__('app.my_account'))
                ->url(fn (): string => \App\Filament\Pages\Account\Profile::getUrl())
                ->icon('heroicon-o-user-circle'),
            // ...
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->bootUsing(function () {
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
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\EnsureStaffRole::class,
            ])
            ->spa()
            ->font('Poppins')
            ->sidebarFullyCollapsibleOnDesktop();
    }
}