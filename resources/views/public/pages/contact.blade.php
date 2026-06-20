@extends('public.layouts.app')

@section('title', $page->meta_title ?: ($page->title . ' - ' . ($themeVars['site_name'] ?? 'School')))
@section('description', $page->meta_description ?: $page->title)

@php
    $officeHours = $page->getSetting('office_hours', []);
    $hasContactInfo = !empty($themeVars['contact_address'])
        || !empty($themeVars['contact_location'])
        || !empty($themeVars['contact_email'])
        || !empty($themeVars['contact_phone']);
    $hasSidebar = $hasContactInfo || count($officeHours);
    $hasMap = isset($themeVars['contact_latitude'], $themeVars['contact_longitude'])
        && $themeVars['contact_latitude'] && $themeVars['contact_longitude'];
@endphp

@section('content')
    <!-- Page Header -->
    <section class="bg-white border-b border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="max-w-3xl">
                <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight text-zinc-900 mb-4">
                    {{ $page->getSetting('header_title', $page->title) }}
                </h1>
                <p class="text-lg text-zinc-600 leading-relaxed">
                    {{ $page->getSetting('header_subtitle', __('Reach out to us for any questions or inquiries.')) }}
                </p>
            </div>
        </div>
    </section>

    <section class="bg-zinc-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="{{ $hasSidebar ? 'grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12' : 'max-w-2xl' }}">
                <!-- Contact Information -->
                @if($hasSidebar)
                <div class="lg:col-span-2 space-y-6">
                    @if($hasContactInfo)
                    <div class="bg-white border border-zinc-200 rounded-xl p-6">
                        <h2 class="text-base font-semibold text-zinc-900 mb-5">{{ __('Contact information') }}</h2>
                        <ul class="space-y-5">
                            @if(isset($themeVars['contact_address']) && $themeVars['contact_address'])
                                <li class="flex items-start gap-3">
                                    <span class="material-icons-round text-zinc-400 mt-0.5">location_on</span>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 mb-0.5">{{ __('Address') }}</p>
                                        <p class="text-sm text-zinc-600 leading-relaxed">{{ $themeVars['contact_address'] }}</p>
                                    </div>
                                </li>
                            @endif

                            @if(isset($themeVars['contact_location']) && $themeVars['contact_location'])
                                <li class="flex items-start gap-3">
                                    <span class="material-icons-round text-zinc-400 mt-0.5">place</span>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 mb-0.5">{{ __('app.location_city') }}</p>
                                        <p class="text-sm text-zinc-600 leading-relaxed">{{ $themeVars['contact_location'] }}</p>
                                    </div>
                                </li>
                            @endif

                            @if(isset($themeVars['contact_email']) && $themeVars['contact_email'])
                                <li class="flex items-start gap-3">
                                    <span class="material-icons-round text-zinc-400 mt-0.5">email</span>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 mb-0.5">{{ __('Email') }}</p>
                                        <a href="mailto:{{ $themeVars['contact_email'] }}"
                                           class="text-sm text-primary-700 hover:text-primary-800 transition-colors duration-150 break-all">
                                            {{ $themeVars['contact_email'] }}
                                        </a>
                                    </div>
                                </li>
                            @endif

                            @if(isset($themeVars['contact_phone']) && $themeVars['contact_phone'])
                                <li class="flex items-start gap-3">
                                    <span class="material-icons-round text-zinc-400 mt-0.5">phone</span>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 mb-0.5">{{ __('Phone') }}</p>
                                        <a href="tel:{{ $themeVars['contact_phone'] }}"
                                           class="text-sm text-primary-700 hover:text-primary-800 transition-colors duration-150" dir="ltr">
                                            {{ $themeVars['contact_phone'] }}
                                        </a>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                    @endif

                    @if(count($officeHours))
                        <div class="bg-white border border-zinc-200 rounded-xl p-6">
                            <h2 class="text-base font-semibold text-zinc-900 mb-5">{{ __('Office hours') }}</h2>
                            <ul class="space-y-2.5">
                                @foreach($officeHours as $entry)
                                    <li class="flex items-center justify-between gap-4 text-sm">
                                        <span class="text-zinc-600">{{ $entry['label'] ?? '' }}</span>
                                        <span class="font-medium text-zinc-900">{{ $entry['value'] ?? '' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                @endif

                <!-- Contact Form -->
                <div class="{{ $hasSidebar ? 'lg:col-span-3' : '' }}">
                    <div class="bg-white border border-zinc-200 rounded-xl p-6 sm:p-8">
                        <h2 class="text-base font-semibold text-zinc-900 mb-1.5">{{ __('Send us a message') }}</h2>
                        <p class="text-sm text-zinc-600 mb-6">
                            {{ $page->getSetting('form_intro', __('Fill out the form below and we will get back to you as soon as possible.')) }}
                        </p>

                        @livewire('contact-form')
                    </div>
                </div>
            </div>

            <!-- Map Section -->
            @if($hasMap)
                <div class="mt-8">
                    <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
                        <div id="map" class="h-96"></div>
                    </div>
                </div>

                @push('styles')
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
                          crossorigin="">
                @endpush

                @push('scripts')
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                            crossorigin=""></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const latitude = {{ $themeVars['contact_latitude'] }};
                            const longitude = {{ $themeVars['contact_longitude'] }};

                            const map = L.map('map').setView([latitude, longitude], 15);

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                                maxZoom: 19,
                            }).addTo(map);

                            const marker = L.marker([latitude, longitude]).addTo(map);

                            @if(isset($themeVars['school_name']) && $themeVars['school_name'])
                                marker.bindPopup(@js(
                                    '<strong>' . e($themeVars['school_name']) . '</strong>'
                                    . (!empty($themeVars['contact_address']) ? '<br>' . e($themeVars['contact_address']) : '')
                                    . (!empty($themeVars['contact_phone']) ? '<br>' . e($themeVars['contact_phone']) : '')
                                )).openPopup();
                            @endif
                        });
                    </script>
                @endpush
            @endif
        </div>
    </section>
@endsection
