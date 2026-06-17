@props(['tabs' => []])

<div class="mb-6 overflow-x-auto">
    <div class="inline-flex min-w-full sm:min-w-0 items-center justify-start sm:justify-center p-1 bg-gray-100 rounded-lg border border-gray-200 dark:bg-gray-900 dark:border-white/10">
        @foreach($tabs as $tab)
            <a href="{{ $tab['url'] }}" 
               class="flex-1 sm:flex-none inline-flex items-center justify-center whitespace-nowrap px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ $tab['active'] 
                   ? 'bg-white text-primary-600 shadow-sm border border-gray-200 dark:bg-gray-800 dark:text-primary-400 dark:border-gray-700' 
                   : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-white/5' }}">
                <x-filament::icon 
                    icon="{{ $tab['icon'] }}" 
                    class="w-4 h-4 me-2 {{ $tab['active'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}" />
                {{ $tab['name'] }}
            </a>
        @endforeach
    </div>
</div>