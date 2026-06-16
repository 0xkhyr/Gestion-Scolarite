@extends('public.layouts.app')

@section('title', $page->meta_title ?: ($page->title . ' - ' . ($themeVars['site_name'] ?? 'School')))
@section('description', $page->meta_description ?: $page->title)

@php
    $highlights = $page->getSetting('highlights', []);
    $team = $page->getSetting('team', []);
@endphp

@section('content')
    <!-- Page Header -->
    <section class="bg-white border-b border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="max-w-3xl">
                <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight text-zinc-900 mb-4">
                    {{ $page->getSetting('header_title', $page->title) }}
                </h1>
                @if($page->getSetting('header_subtitle'))
                    <p class="text-lg text-zinc-600 leading-relaxed">
                        {{ $page->getSetting('header_subtitle') }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            @if(trim(strip_tags($page->getContent())))
                <div class="prose prose-zinc max-w-3xl">
                    {!! $page->getContent() !!}
                </div>
            @endif

            <!-- Highlights (admin-defined cards: icon, title, description) -->
            @if(count($highlights))
                <div class="grid sm:grid-cols-2 gap-5 {{ trim(strip_tags($page->getContent())) ? 'mt-14' : '' }}">
                    @foreach($highlights as $highlight)
                        <div class="bg-white border border-zinc-200 rounded-xl p-6 hover:border-zinc-300 hover:shadow-sm transition-all duration-150">
                            <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mb-4">
                                <span class="material-icons-round text-primary-700">{{ $highlight['icon'] ?? 'check_circle' }}</span>
                            </div>
                            <h2 class="text-base font-semibold text-zinc-900 mb-1.5">{{ $highlight['title'] ?? '' }}</h2>
                            <p class="text-sm text-zinc-600 leading-relaxed">{{ $highlight['description'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Team (only rendered when the school has added members in the admin) -->
    @if(count($team))
        <section class="bg-zinc-50 border-t border-zinc-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
                <div class="max-w-2xl mb-12">
                    <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-zinc-900 mb-3">
                        {{ $page->getSetting('team_heading', __('Our team')) }}
                    </h2>
                    @if($page->getSetting('team_subheading'))
                        <p class="text-base text-zinc-600 leading-relaxed">
                            {{ $page->getSetting('team_subheading') }}
                        </p>
                    @endif
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($team as $member)
                        <div class="bg-white border border-zinc-200 rounded-xl p-6">
                            @if(!empty($member['photo']))
                                <img src="{{ Storage::url($member['photo']) }}"
                                     alt="{{ $member['name'] ?? '' }}"
                                     class="w-14 h-14 rounded-full object-cover mb-4">
                            @else
                                <div class="w-14 h-14 rounded-full bg-primary-100 flex items-center justify-center mb-4">
                                    <span class="material-icons-round text-primary-700">person</span>
                                </div>
                            @endif
                            <h3 class="text-base font-semibold text-zinc-900">{{ $member['name'] ?? '' }}</h3>
                            @if(!empty($member['role']))
                                <p class="text-sm text-zinc-500 mb-2">{{ $member['role'] }}</p>
                            @endif
                            @if(!empty($member['bio']))
                                <p class="text-sm text-zinc-600 leading-relaxed">{{ $member['bio'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Call to Action -->
    <section class="bg-white border-t border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="max-w-2xl">
                <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-zinc-900 mb-3">
                    {{ $page->getSetting('cta_heading', __('Want to know more?')) }}
                </h2>
                <p class="text-base text-zinc-600 leading-relaxed mb-6">
                    {{ $page->getSetting('cta_text', __('Get in touch and we will answer your questions.')) }}
                </p>
                <a href="{{ route('page.show', 'contact') }}"
                   class="inline-flex items-center justify-center bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150">
                    {{ $page->getSetting('cta_label', __('Contact us')) }}
                </a>
            </div>
        </div>
    </section>
@endsection
