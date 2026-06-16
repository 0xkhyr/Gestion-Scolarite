@extends('public.layouts.app')

@section('title', $page->meta_title ?: ($page->title . ' - ' . ($themeVars['site_name'] ?? 'School')))
@section('description', $page->meta_description ?: ($themeVars['site_name'] ?? 'School Management System'))

@php
    $features = $page->getSetting('features', [
        ['icon' => 'school', 'title' => 'Qualified Teachers', 'description' => 'Experienced educators committed to every student\'s progress.'],
        ['icon' => 'menu_book', 'title' => 'Complete Curriculum', 'description' => 'A structured program covering all core subjects and levels.'],
        ['icon' => 'insights', 'title' => 'Progress Tracking', 'description' => 'Parents and students follow grades and attendance in real time.'],
    ]);
    $showPortals = $page->getSetting('show_portals', true);
@endphp

@section('content')
    <!-- Hero Section -->
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div>
                    <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-zinc-900 leading-tight mb-5">
                        {{ $page->getSetting('hero_title', __('Welcome to') . ' ' . ($themeVars['site_name'] ?? __('our school'))) }}
                    </h1>
                    <p class="text-lg text-zinc-600 leading-relaxed mb-8">
                        {{ $page->getSetting('hero_subtitle', 'A school management platform connecting students, parents and teachers in one place.') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ $page->getSetting('cta_primary_url', route('page.show', 'about')) }}"
                           class="inline-flex items-center justify-center bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150">
                            {{ $page->getSetting('cta_primary_label', __('Learn more')) }}
                        </a>
                        <a href="{{ $page->getSetting('cta_secondary_url', route('page.show', 'contact')) }}"
                           class="inline-flex items-center justify-center border border-zinc-300 hover:border-zinc-400 text-zinc-700 px-5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150">
                            {{ $page->getSetting('cta_secondary_label', __('Contact us')) }}
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
                            <p class="text-sm text-zinc-400">{{ $themeVars['site_name'] ?? __('Our school') }}</p>
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
                    {{ $page->getSetting('features_heading', __('What we offer')) }}
                </h2>
                <p class="text-base text-zinc-600 leading-relaxed">
                    {{ $page->getSetting('features_subheading', __('Everything a school needs to support its students.')) }}
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
                        {{ $page->getSetting('portals_heading', __('Access your portal')) }}
                    </h2>
                    <p class="text-base text-zinc-600 leading-relaxed">
                        {{ $page->getSetting('portals_subheading', __('Sign in to view grades, schedules and school services.')) }}
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div class="bg-white border border-zinc-200 rounded-xl p-6 hover:border-zinc-300 hover:shadow-sm transition-all duration-150">
                        <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mb-4">
                            <span class="material-icons-round text-primary-700">school</span>
                        </div>
                        <h3 class="text-base font-semibold text-zinc-900 mb-1.5">{{ __('Students') }}</h3>
                        <p class="text-sm text-zinc-600 leading-relaxed mb-4">{{ __('Grades, assignments, schedules and school updates.') }}</p>
                        <a href="/login?role=student" class="text-sm font-medium text-primary-700 hover:text-primary-800 transition-colors duration-150">
                            {{ __('Student sign in') }}
                        </a>
                    </div>

                    <div class="bg-white border border-zinc-200 rounded-xl p-6 hover:border-zinc-300 hover:shadow-sm transition-all duration-150">
                        <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mb-4">
                            <span class="material-icons-round text-primary-700">family_restroom</span>
                        </div>
                        <h3 class="text-base font-semibold text-zinc-900 mb-1.5">{{ __('Parents') }}</h3>
                        <p class="text-sm text-zinc-600 leading-relaxed mb-4">{{ __('Follow your child\'s progress and contact teachers.') }}</p>
                        <a href="/login?role=parent" class="text-sm font-medium text-primary-700 hover:text-primary-800 transition-colors duration-150">
                            {{ __('Parent sign in') }}
                        </a>
                    </div>

                    <div class="bg-white border border-zinc-200 rounded-xl p-6 hover:border-zinc-300 hover:shadow-sm transition-all duration-150">
                        <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mb-4">
                            <span class="material-icons-round text-primary-700">badge</span>
                        </div>
                        <h3 class="text-base font-semibold text-zinc-900 mb-1.5">{{ __('Staff') }}</h3>
                        <p class="text-sm text-zinc-600 leading-relaxed mb-4">{{ __('Administrative tools, grade entry and resources.') }}</p>
                        <a href="/login" class="text-sm font-medium text-primary-700 hover:text-primary-800 transition-colors duration-150">
                            {{ __('Staff sign in') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
