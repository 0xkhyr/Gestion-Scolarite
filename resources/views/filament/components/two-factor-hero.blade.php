<!-- Modern Hero Section for Two-Factor Setup -->
<div class="relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-gradient-to-br from-primary-50 via-white to-primary-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 opacity-50"></div>
    
    <!-- Content -->
    <div class="relative p-8 text-center">
        <!-- Security Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 mb-6 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl shadow-lg">
            <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        
        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
            {{ $title }}
        </h1>
        
        <!-- Subtitle -->
        <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-2xl mx-auto leading-relaxed">
            {{ $subtitle }}
        </p>
        
        <!-- Status Badge -->
        <div class="inline-flex items-center gap-3 px-6 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-sm">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-amber-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('app.status') }}</span>
            </div>
            <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>
            <span class="text-sm font-medium text-amber-600 dark:text-amber-400">{{ $status }}</span>
        </div>
    </div>
</div>