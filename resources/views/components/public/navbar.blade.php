@props(['themeVars'])

@php
    $navigationPages = \App\Models\Page::getNavigationPages();
@endphp

<nav class="sticky top-0 z-40 bg-white border-b border-zinc-200" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <!-- Logo and Site Name -->
            <a href="{{ route('homepage') }}" class="flex items-center gap-3">
                @if(isset($themeVars['logo_url']) && $themeVars['logo_url'])
                    <img src="{{ Storage::url($themeVars['logo_url']) }}"
                         alt="{{ $themeVars['site_name'] ?? 'Logo' }}"
                         class="h-8 w-8 object-contain">
                @endif
                <span class="text-base font-semibold text-zinc-900">
                    {{ $themeVars['site_name'] ?? 'School Management System' }}
                </span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('homepage') }}"
                   class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                          {{ request()->routeIs('homepage') ? 'text-primary-700' : 'text-zinc-600 hover:text-zinc-900' }}">
                    {{ __('Home') }}
                </a>

                @foreach($navigationPages as $navPage)
                    @if($navPage->slug !== 'homepage')
                        <a href="{{ route('page.show', $navPage->slug) }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150
                                  {{ request()->route('slug') === $navPage->slug ? 'text-primary-700' : 'text-zinc-600 hover:text-zinc-900' }}">
                            {{ $navPage->title }}
                        </a>
                    @endif
                @endforeach

                @auth
                    <div class="relative ms-3" x-data="{ dropdownOpen: false }">
                        <button @click="dropdownOpen = !dropdownOpen"
                                class="flex items-center gap-1.5 px-3 py-2 rounded-md text-sm font-medium text-zinc-700 hover:text-zinc-900 transition-colors duration-150">
                            {{ Auth::user()->name ?? __('Dashboard') }}
                            <span class="material-icons-round !text-base text-zinc-400">expand_more</span>
                        </button>
                        <div x-show="dropdownOpen"
                             @click.away="dropdownOpen = false"
                             x-transition.opacity.duration.150ms
                             x-cloak
                             class="absolute end-0 mt-1 w-44 rounded-lg border border-zinc-200 bg-white py-1 shadow-sm">
                            <a href="/admin" class="block px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                {{ __('Dashboard') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-start text-sm text-zinc-700 hover:bg-zinc-50">
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="/login"
                       class="ms-3 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150">
                        {{ __('Login') }}
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="md:hidden p-2 rounded-md text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 transition-colors duration-150">
                <span class="material-icons-round" x-text="mobileMenuOpen ? 'close' : 'menu'"></span>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="mobileMenuOpen"
         x-transition.opacity.duration.150ms
         x-cloak
         class="md:hidden border-t border-zinc-200 bg-white">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('homepage') }}"
               class="block px-3 py-2 rounded-md text-sm font-medium
                      {{ request()->routeIs('homepage') ? 'text-primary-700 bg-zinc-50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                {{ __('Home') }}
            </a>

            @foreach($navigationPages as $navPage)
                @if($navPage->slug !== 'homepage')
                    <a href="{{ route('page.show', $navPage->slug) }}"
                       class="block px-3 py-2 rounded-md text-sm font-medium
                              {{ request()->route('slug') === $navPage->slug ? 'text-primary-700 bg-zinc-50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                        {{ $navPage->title }}
                    </a>
                @endif
            @endforeach

            <div class="pt-3 mt-3 border-t border-zinc-200">
                @auth
                    <a href="/admin" class="block px-3 py-2 rounded-md text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
                        {{ __('Dashboard') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-3 py-2 rounded-md text-start text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50">
                            {{ __('Logout') }}
                        </button>
                    </form>
                @else
                    <a href="/login"
                       class="block px-3 py-2 rounded-lg text-center text-sm font-medium bg-primary-600 hover:bg-primary-700 text-white transition-colors duration-150">
                        {{ __('Login') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
