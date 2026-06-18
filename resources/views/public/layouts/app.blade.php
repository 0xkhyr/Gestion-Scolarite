<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $themeVars['site_name'] ?? 'School Management System')</title>
    <meta name="description" content="@yield('description', $themeVars['site_name'] ?? 'School Management System')">

    <!-- SEO Meta Tags -->
    <meta name="keywords" content="@yield('keywords', 'school, education, students, teachers')">
    <meta name="author" content="{{ $themeVars['site_name'] ?? 'School Management System' }}">

    <!-- Open Graph Tags -->
    <meta property="og:title" content="@yield('og_title', $themeVars['site_name'] ?? 'School Management System')">
    <meta property="og:description" content="@yield('og_description', $themeVars['site_name'] ?? 'School Management System')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if(isset($themeVars['logo_url']) && $themeVars['logo_url'])
        <meta property="og:image" content="{{ Storage::url($themeVars['logo_url']) }}">
    @endif

    <!-- Favicon -->
    @if(isset($themeVars['logo_url']) && $themeVars['logo_url'])
        <link rel="icon" href="{{ Storage::url($themeVars['logo_url']) }}" type="image/x-icon">
    @endif

    <!-- Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css'])

    <!-- Dynamic Theme CSS -->
    <link rel="stylesheet" href="{{ route('theme.css') }}">

    <!-- Additional Styles -->
    @stack('styles')

    <!-- Tenant theme: the admin-chosen brand color drives the whole primary
         scale, so changing one setting re-themes the site without a rebuild. -->
    <style>
        :root {
            --color-primary: {{ $themeVars['primary_color'] ?? '#4F6BED' }};
            --color-secondary: {{ $themeVars['secondary_color'] ?? '#3D55C8' }};

            --color-primary-50: color-mix(in srgb, var(--color-primary) 5%, white);
            --color-primary-100: color-mix(in srgb, var(--color-primary) 10%, white);
            --color-primary-200: color-mix(in srgb, var(--color-primary) 22%, white);
            --color-primary-300: color-mix(in srgb, var(--color-primary) 38%, white);
            --color-primary-400: color-mix(in srgb, var(--color-primary) 62%, white);
            --color-primary-500: color-mix(in srgb, var(--color-primary) 85%, white);
            --color-primary-600: var(--color-primary);
            --color-primary-700: color-mix(in srgb, var(--color-primary) 86%, black);
            --color-primary-800: color-mix(in srgb, var(--color-primary) 72%, black);
            --color-primary-900: color-mix(in srgb, var(--color-primary) 58%, black);

            /* Legacy aliases used by theme.css and older views */
            --primary-color: var(--color-primary);
            --secondary-color: var(--color-secondary);
        }

        [x-cloak] {
            display: none !important;
        }

        .material-icons-round {
            font-size: 1.25rem;
            line-height: 1;
            vertical-align: middle;
        }

        /* Legacy material-* classes redefined as flat, clean styles until
           all public pages are migrated off them. */
        .material-surface {
            background-color: #fafafa;
        }

        .material-card {
            background-color: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 0.75rem;
        }

        .material-button {
            transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease;
        }
    </style>

    @livewireStyles
</head>

<body class="bg-white text-zinc-900 antialiased">
    <!-- Navigation -->
    <x-public.navbar :theme-vars="$themeVars" />

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <x-public.footer :theme-vars="$themeVars" />

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    @livewireScripts

    <!-- Additional Scripts -->
    @stack('scripts')

    <!-- Toast Notifications -->
    @if(session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition.opacity
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 z-50 flex items-center gap-3 rounded-lg border border-zinc-200 bg-white px-4 py-3 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            <p class="text-sm text-zinc-700">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition.opacity
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 z-50 flex items-center gap-3 rounded-lg border border-zinc-200 bg-white px-4 py-3 shadow-sm">
            <span class="h-2 w-2 rounded-full bg-red-500"></span>
            <p class="text-sm text-zinc-700">{{ session('error') }}</p>
        </div>
    @endif
</body>
</html>
