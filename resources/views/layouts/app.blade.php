<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Fonts -->
    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="favicon">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
        </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- NProgress CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <style>
        #nprogress .bar {
            background: #0d6efd !important;
            height: 3px !important;
            z-index: 99999 !important;
        }

        /* Bootstrap Primary Blue */
        #nprogress .peg {
            box-shadow: 0 0 10px #0d6efd, 0 0 5px #0d6efd !important;
        }

        #nprogress .spinner-icon {
            border-top-color: #0d6efd !important;
            border-left-color: #0d6efd !important;
        }
    </style>
</head>
<style>
    .container {
        background-color: #fff;
        padding: 8px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
</style>

<body>

    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm fixed-top" id="allAction">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'École') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('enseignant.connexion') }}">{{ __('Connexion') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('enseignant.inscription') }}">{{ __('Inscription') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('enseignant.deconnexion') }}"
                                        onclick="event.preventDefault();
                                                                                 document.getElementById('logout-form').submit();">
                                        {{ __('Déconnexion ') }} <i class="bi bi-box-arrow-right"></i>
                                    </a>

                                    <form id="logout-form" action="{{ route('enseignant.deconnexion') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div id="loading-spinner"></div>
            @yield('content')
        </main>
    </div>
    <!-- NProgress JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // NProgress Configuration
            NProgress.configure({ showSpinner: false });

            // Page Transition Loading
            window.addEventListener('beforeunload', function () {
                NProgress.start();
            });

            // Also trigger on link clicks for immediate feedback
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link &&
                    !link.target &&
                    !link.hasAttribute('download') &&
                    link.href &&
                    link.href.startsWith(window.location.origin) &&
                    !link.href.includes('#') &&
                    !e.ctrlKey && !e.metaKey && !e.shiftKey && !e.altKey
                ) {
                    NProgress.start();
                }
            });

            window.addEventListener('load', function () {
                NProgress.done();
            });

            // Global Button Loading
            document.addEventListener('submit', function (e) {
                const form = e.target;
                const submitBtn = form.querySelector('[type="submit"], button:not([type="button"]):not([type="reset"])');

                if (submitBtn && !submitBtn.classList.contains('no-loading')) {
                    if (submitBtn.disabled) {
                        e.preventDefault();
                        return;
                    }

                    submitBtn.dataset.originalContent = submitBtn.innerHTML;
                    const loadingText = submitBtn.dataset.loadingText || 'Chargement...';
                    submitBtn.disabled = true;
                    // Add spinner if bootstrap is available or use custom
                    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${loadingText}`;
                }
            });

            // Restore button state (bfcache)
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    const submitBtns = document.querySelectorAll('[type="submit"][disabled], button[disabled]');
                    submitBtns.forEach(btn => {
                        if (btn.dataset.originalContent) {
                            btn.innerHTML = btn.dataset.originalContent;
                            btn.disabled = false;
                        }
                    });
                    NProgress.done();
                }
            });
        });
    </script>
</body>

</html>