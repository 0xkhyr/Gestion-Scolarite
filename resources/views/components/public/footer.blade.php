@props(['themeVars'])

@php
    $footerPages = \App\Models\Page::enabled()->public()->ordered()->take(4)->get();
    $footerDescription = \App\Models\SiteSetting::get('footer_description', '');
@endphp

<footer class="border-t border-zinc-200 bg-zinc-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <!-- School Information -->
            <div class="sm:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    @if(isset($themeVars['logo_url']) && $themeVars['logo_url'])
                        <img src="{{ Storage::url($themeVars['logo_url']) }}"
                             alt="{{ $themeVars['site_name'] ?? 'Logo' }}"
                             class="h-8 w-8 object-contain">
                    @endif
                    <span class="text-base font-semibold text-zinc-900">
                        {{ $themeVars['site_name'] ?? 'School Management System' }}
                    </span>
                </div>
                @if($footerDescription)
                    <p class="text-sm text-zinc-600 leading-relaxed max-w-md mb-4">
                        {{ $footerDescription }}
                    </p>
                @endif
                @if(isset($themeVars['contact_address']) && $themeVars['contact_address'])
                    <p class="text-sm text-zinc-500 leading-relaxed max-w-md">
                        {{ $themeVars['contact_address'] }}
                    </p>
                @endif
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-sm font-semibold text-zinc-900 mb-4">{{ __('Pages') }}</h4>
                <nav aria-label="Footer navigation">
                    <ul class="space-y-2.5">
                        <li>
                            <a href="{{ route('homepage') }}"
                               class="text-sm text-zinc-600 hover:text-zinc-900 transition-colors duration-150">
                                {{ __('Home') }}
                            </a>
                        </li>
                        @foreach($footerPages as $page)
                            @if($page->slug !== 'homepage')
                                <li>
                                    <a href="{{ route('page.show', $page->slug) }}"
                                       class="text-sm text-zinc-600 hover:text-zinc-900 transition-colors duration-150">
                                        {{ $page->title }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </nav>
            </div>

            <!-- Contact Information -->
            <div>
                <h4 class="text-sm font-semibold text-zinc-900 mb-4">{{ __('Contact') }}</h4>
                <ul class="space-y-2.5">
                    @if(isset($themeVars['contact_email']) && $themeVars['contact_email'])
                        <li>
                            <a href="mailto:{{ $themeVars['contact_email'] }}"
                               class="text-sm text-zinc-600 hover:text-zinc-900 transition-colors duration-150 break-all">
                                {{ $themeVars['contact_email'] }}
                            </a>
                        </li>
                    @endif
                    @if(isset($themeVars['contact_phone']) && $themeVars['contact_phone'])
                        <li>
                            <a href="tel:{{ $themeVars['contact_phone'] }}"
                               class="text-sm text-zinc-600 hover:text-zinc-900 transition-colors duration-150" dir="ltr">
                                {{ $themeVars['contact_phone'] }}
                            </a>
                        </li>
                    @endif
                    <li class="pt-1">
                        <a href="{{ route('page.show', 'contact') }}"
                           class="text-sm font-medium text-primary-700 hover:text-primary-800 transition-colors duration-150">
                            {{ __('Send us a message') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="border-t border-zinc-200 mt-10 pt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-sm text-zinc-500">
                &copy; {{ date('Y') }} {{ $themeVars['site_name'] ?? 'School Management System' }}. {{ __('All rights reserved.') }}
            </p>

            <!-- Language Switcher -->
            <div class="flex items-center gap-1">
                @foreach(['en' => 'EN', 'fr' => 'FR', 'ar' => 'AR'] as $locale => $label)
                    <a href="{{ route('lang.switch', $locale) }}"
                       class="px-2.5 py-1.5 rounded-md text-sm transition-colors duration-150
                              {{ app()->getLocale() === $locale
                                 ? 'font-medium text-zinc-900 bg-zinc-200/70'
                                 : 'text-zinc-500 hover:text-zinc-900' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
