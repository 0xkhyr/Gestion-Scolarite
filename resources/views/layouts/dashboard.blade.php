<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1a73e8">
    <meta name="description" content="Système de gestion scolaire moderne et performant">

    <title>{{ config('app.name', 'Gestion Scolaire') }} - @yield('title', 'Dashboard')</title>

    <!-- Preconnect to external domains for better performance -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://kit.fontawesome.com" crossorigin>

    <!-- DNS Prefetch for faster loading -->
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <!-- Bootstrap CSS - Essential for layout -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Critical CSS - Load after Bootstrap for overrides -->
    <link rel="stylesheet" href="{{ asset('css/google-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile-header.css') }}">
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/663a36fa19.js" crossorigin="anonymous"></script>
    
    <!-- Flag Icons - Defer non-critical -->
    <link rel="preload" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css"></noscript>

    <!-- Fonts - Load with display swap for better performance -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Additional Component CSS -->
    <link rel="stylesheet" href="{{ asset('css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">

    @stack('styles')
</head>

<body>
    <!-- Mobile Header -->
    <header class="mobile-header">
        <div class="mobile-header-container">
            <!-- Menu Toggle -->
            <button class="mobile-menu-btn" type="button" aria-label="{{ __('app.ouvrir_menu') }}">
                <svg class="mobile-menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12h18M3 6h18M3 18h18"/>
                </svg>
            </button>

            <!-- Brand -->
            @php
                $dashboardRoute = route('accueil');
                if (auth()->check()) {
                    if (auth()->user()->isAdmin()) {
                        $dashboardRoute = route('admin.dashboard');
                    } elseif (auth()->user()->isTeacher()) {
                        $dashboardRoute = route('enseignant.dashboard');
                    } elseif (auth()->user()->isStudent()) {
                        $dashboardRoute = route('etudiant.dashboard');
                    }
                }
            @endphp
            <a href="{{ $dashboardRoute }}" class="mobile-brand">
                <div class="mobile-brand-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>
                    </svg>
                </div>
                <span class="mobile-brand-text">{{ config('app.name', 'Gestion Scolaire') }}</span>
            </a>



            <!-- Actions -->
            <div class="mobile-actions">
                
                <!-- Language Switcher -->
                @php
                    // Choose the correct lang-switch route depending on the current area
                    $adminPrefix = trim(config('admin.prefix', 'control-panel'), '/');
                    if ($adminPrefix && (request()->is($adminPrefix) || request()->is($adminPrefix.'/*'))) {
                        $langRoute = 'admin.lang.switch';
                    } elseif (request()->is('enseignant') || request()->is('enseignant/*')) {
                        $langRoute = 'enseignant.lang.switch';
                    } else {
                        $langRoute = 'lang.switch';
                    }
                @endphp
                <div class="mobile-action-dropdown">
                    <button class="mobile-action-btn" type="button" data-bs-toggle="dropdown" aria-label="{{ __('app.changer_langue') }}">
                        @if(app()->getLocale() === 'fr')
                            <span class="flag-icon fi fi-fr"></span>
                        @elseif(app()->getLocale() === 'ar')
                            <span class="flag-icon fi fi-sa"></span>
                        @else
                            <span class="flag-icon fi fi-us"></span>
                        @endif
                    </button>
                    <ul class="mobile-dropdown-menu dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="mobile-dropdown-item {{ app()->getLocale() === 'fr' ? 'active' : '' }}" href="{{ \Illuminate\Support\Facades\Route::has($langRoute) ? route($langRoute, 'fr') : url('/langue/fr') }}">
                                <span class="fi fi-fr"></span>
                                <span>Français</span>
                            </a>
                        </li>
                        <li>
                            <a class="mobile-dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{ \Illuminate\Support\Facades\Route::has($langRoute) ? route($langRoute, 'ar') : url('/langue/ar') }}">
                                <span class="fi fi-sa"></span>
                                <span>العربية</span>
                            </a>
                        </li>
                        <li>
                            <a class="mobile-dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ \Illuminate\Support\Facades\Route::has($langRoute) ? route($langRoute, 'en') : url('/langue/en') }}">
                                <span class="fi fi-us"></span>
                                <span>English</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- notifications -->
                <div class="mobile-action-dropdown">
                    <x-dashboard.notifications />
                </div>



                <!-- User Menu -->
                <div class="mobile-action-dropdown">
                    <button class="mobile-action-btn mobile-user-btn" type="button" data-bs-toggle="dropdown" aria-label="{{ __('app.menu_utilisateur') }}">
                        @php
                            $currentUser = auth()->user();
                            $userName = $currentUser->name ?? 'User';
                            $userEmail = $currentUser->email ?? '';
                            $initials = collect(explode(' ', $userName))->map(fn($word) => mb_substr($word, 0, 1))->take(2)->join('');
                            
                            $profileRoute = '#';
                            if (auth()->check() && method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('teacher')) {
                                $profileRoute = route('enseignant.profil');
                            }
                        @endphp
                        <span class="mobile-user-avatar">{{ $initials }}</span>
                    </button>
                    <ul class="mobile-dropdown-menu dropdown-menu dropdown-menu-end">
                        <li class="mobile-dropdown-header">
                            <div class="mobile-user-name">{{ $userName }}</div>
                            <div class="mobile-user-email">{{ $userEmail }}</div>
                        </li>
                        <li><hr class="mobile-dropdown-divider"></li>
                        <li>
                            <a class="mobile-dropdown-item" href="{{ $profileRoute }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span>{{ __('app.profile') }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="mobile-dropdown-item" href="#">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M12 1v6m0 6v6m9-9h-6m-6 0H3"/>
                                </svg>
                                <span>{{ __('app.settings') }}</span>
                            </a>
                        </li>
                        <li><hr class="mobile-dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('enseignant.deconnexion') }}">
                                @csrf
                                <button type="submit" class="mobile-dropdown-item mobile-dropdown-danger">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14l5-5-5-5m5 5H9"/>
                                    </svg>
                                    <span>{{ __('app.logout') }}</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay"></div>

    <div class="main-wrapper">
        <!-- Sidebar -->
        <x-navigation.sidebar />

        <!-- Main Content -->
        <main class="main-content">
            <!-- Content Header -->
            <div class="content-header">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Left Section: Title & Breadcrumb -->
                    <div class="header-left flex-grow-1">
                        <h1 class="page-title mb-0">@yield('title', 'Dashboard')</h1>
                    </div>

                    <!-- Right Section: Actions & Language Switcher -->
                    <div class="header-right d-flex align-items-center gap-2">
                        <!-- Header Actions (if any) -->
                        @hasSection('header-actions')
                            <div class="header-actions">
                                @yield('header-actions')
                            </div>
                        @endif

                        <!-- Notifications -->
                        <div class="dropdown d-none d-lg-block">
                            <x-dashboard.notifications />
                        </div>

                        <!-- Desktop Language Switcher -->
                        <div class="lang-switcher dropdown d-none d-lg-block">
                            <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-label="{{ __('app.change_language') }}">
                                @if(app()->getLocale() === 'fr')
                                    <span class="fi fi-fr"></span>
                                @elseif(app()->getLocale() === 'ar')
                                    <span class="fi fi-sa"></span>
                                @else
                                    <span class="fi fi-us"></span>
                                @endif
                                <span
                                    class="{{ app()->getLocale() === 'ar' ? 'me-1' : 'ms-1' }}">{{ strtoupper(app()->getLocale()) }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item {{ app()->getLocale() === 'fr' ? 'active' : '' }}"
                                        href="{{ \Illuminate\Support\Facades\Route::has($langRoute) ? route($langRoute, 'fr') : url('/langue/fr') }}">
                                        <span
                                            class="fi fi-fr {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></span>
                                        Français
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}"
                                        href="{{ \Illuminate\Support\Facades\Route::has($langRoute) ? route($langRoute, 'ar') : url('/langue/ar') }}">
                                        <span
                                            class="fi fi-sa {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></span>
                                        العربية
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}"
                                        href="{{ \Illuminate\Support\Facades\Route::has($langRoute) ? route($langRoute, 'en') : url('/langue/en') }}">
                                        <span
                                            class="fi fi-us {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></span>
                                        English
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Body -->
            <div class="content-body">
                <!-- Breadcrumb -->
                @hasSection('breadcrumb')
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                @endif
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ __(session('success')) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ __(session('error')) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ __(session('warning')) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __(session('info')) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Main Content -->
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bootstrap JS - Load with integrity check -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <!-- Alpine.js for Custom Components - Load early for datalists -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Dashboard Core Scripts - Optimized and cached -->
    <script defer src="{{ asset('js/dashboard.js') }}"></script>

    @stack('scripts')
</body>

</html>