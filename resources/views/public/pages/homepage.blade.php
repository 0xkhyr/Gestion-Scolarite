@extends('public.layouts.app')

@section('title', $page->meta_title ?: ($page->title . ' - ' . ($themeVars['site_name'] ?? 'School')))
@section('description', $page->meta_description ?: ($themeVars['site_name'] ?? 'School Management System'))

@php
    $features = $page->getSetting('features', [
        ['icon' => 'school', 'title' => __('app.home_feature_teachers_title'), 'description' => __('app.home_feature_teachers_desc')],
        ['icon' => 'menu_book', 'title' => __('app.home_feature_curriculum_title'), 'description' => __('app.home_feature_curriculum_desc')],
        ['icon' => 'insights', 'title' => __('app.home_feature_tracking_title'), 'description' => __('app.home_feature_tracking_desc')],
    ]);
    $showPortals = $page->getSetting('show_portals', true);
    $loginUrl = route('filament.admin.auth.login');
@endphp

@section('content')
    <!-- Hero Section -->
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div>
                    <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-zinc-900 leading-tight mb-5">
                        {{ $page->getSetting('hero_title', __('app.home_welcome_to') . ' ' . ($themeVars['site_name'] ?? __('app.home_our_school'))) }}
                    </h1>
                    <p class="text-lg text-zinc-600 leading-relaxed mb-8">
                        {{ $page->getSetting('hero_subtitle', __('app.home_hero_subtitle')) }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ $page->getSetting('cta_primary_url', route('page.show', 'about')) }}"
                           class="inline-flex items-center justify-center bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150">
                            {{ $page->getSetting('cta_primary_label', __('app.home_learn_more')) }}
                        </a>
                        <a href="{{ $page->getSetting('cta_secondary_url', route('page.show', 'contact')) }}"
                           class="inline-flex items-center justify-center border border-zinc-300 hover:border-zinc-400 text-zinc-700 px-5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150">
                            {{ $page->getSetting('cta_secondary_label', __('app.home_contact_us')) }}
                        </a>
                    </div>
                </div>

                <!-- Hero Image (admin-uploaded; clean placeholder until one is set) -->
                <div>
                    @if($page->getSetting('hero_image'))
                        <img src="{{ Storage::url($page->getSetting('hero_image')) }}"
                             alt="{{ $themeVars['site_name'] ?? '' }}"
                             class="w-full aspect-[4/3] object-cover rounded-xl border border-zinc-200">
                    @else
                        <div class="w-full aspect-[4/3] rounded-xl border border-zinc-200 bg-zinc-50 flex flex-col items-center justify-center gap-3">
                            <div class="w-14 h-14 rounded-xl bg-primary-100 flex items-center justify-center">
                                <span class="material-icons-round !text-3xl text-primary-700">school</span>
                            </div>
                            <p class="text-sm text-zinc-400">{{ $themeVars['site_name'] ?? __('app.home_our_school') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="bg-zinc-50 border-y border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24">
            <div class="max-w-2xl mb-12">
                <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-zinc-900 mb-3">
                    {{ $page->getSetting('features_heading', __('app.home_features_heading')) }}
                </h2>
                <p class="text-base text-zinc-600 leading-relaxed">
                    {{ $page->getSetting('features_subheading', __('app.home_features_subheading')) }}
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($features as $feature)
                    <div class="bg-white border border-zinc-200 rounded-xl p-6 hover:border-zinc-300 hover:shadow-sm transition-all duration-150">
                        <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mb-4">
                            <span class="material-icons-round text-primary-700">{{ $feature['icon'] ?? 'check_circle' }}</span>
                        </div>
                        <h3 class="text-base font-semibold text-zinc-900 mb-1.5">{{ $feature['title'] ?? '' }}</h3>
                        <p class="text-sm text-zinc-600 leading-relaxed">{{ $feature['description'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>

            @if(trim(strip_tags($page->getContent())))
                <div class="mt-14 prose prose-zinc max-w-none">
                    {!! $page->getContent() !!}
                </div>
            @endif
        </div>
    </section>

    <!-- Portals Section -->
    @if($showPortals)
        <section class="bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24">
                <div class="max-w-2xl mb-12">
                    <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-zinc-900 mb-3">
                        {{ $page->getSetting('portals_heading', __('app.home_portals_heading')) }}
                    </h2>
                    <p class="text-base text-zinc-600 leading-relaxed">
                        {{ $page->getSetting('portals_subheading', __('app.home_portals_subheading')) }}
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div class="bg-white border border-zinc-200 rounded-xl p-6 hover:border-zinc-300 hover:shadow-sm transition-all duration-150">
                        <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mb-4">
                            <span class="material-icons-round text-primary-700">school</span>
                        </div>
                        <h3 class="text-base font-semibold text-zinc-900 mb-1.5">{{ __('app.home_portal_students') }}</h3>
                        <p class="text-sm text-zinc-600 leading-relaxed mb-4">{{ __('app.home_portal_students_desc') }}</p>
                        <a href="{{ $loginUrl }}" class="text-sm font-medium text-primary-700 hover:text-primary-800 transition-colors duration-150">
                            {{ __('app.home_portal_students_cta') }}
                        </a>
                    </div>

                    <div class="bg-white border border-zinc-200 rounded-xl p-6 hover:border-zinc-300 hover:shadow-sm transition-all duration-150">
                        <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mb-4">
                            <span class="material-icons-round text-primary-700">family_restroom</span>
                        </div>
                        <h3 class="text-base font-semibold text-zinc-900 mb-1.5">{{ __('app.home_portal_parents') }}</h3>
                        <p class="text-sm text-zinc-600 leading-relaxed mb-4">{{ __('app.home_portal_parents_desc') }}</p>
                        <a href="{{ $loginUrl }}" class="text-sm font-medium text-primary-700 hover:text-primary-800 transition-colors duration-150">
                            {{ __('app.home_portal_parents_cta') }}
                        </a>
                    </div>

                    <div class="bg-white border border-zinc-200 rounded-xl p-6 hover:border-zinc-300 hover:shadow-sm transition-all duration-150">
                        <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mb-4">
                            <span class="material-icons-round text-primary-700">badge</span>
                        </div>
                        <h3 class="text-base font-semibold text-zinc-900 mb-1.5">{{ __('app.home_portal_staff') }}</h3>
                        <p class="text-sm text-zinc-600 leading-relaxed mb-4">{{ __('app.home_portal_staff_desc') }}</p>
                        <a href="{{ $loginUrl }}" class="text-sm font-medium text-primary-700 hover:text-primary-800 transition-colors duration-150">
                            {{ __('app.home_portal_staff_cta') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
