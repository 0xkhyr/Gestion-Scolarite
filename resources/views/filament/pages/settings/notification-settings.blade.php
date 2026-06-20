<x-filament-panels::page>
    <div class="space-y-6">
        <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('app.system_wide_notification_control') }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('app.system_wide_notification_control_desc') }}
            </p>
        </div>
        
        
        @if (method_exists($this, 'form'))
            <form wire:submit="save">
                {{ $this->form }}
            </form>
        @endif
    </div>
</x-filament-panels::page>
