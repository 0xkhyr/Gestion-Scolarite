<x-filament-panels::page>
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-4 dark:border-white/10">
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('app.notifications_page_subtitle') }}</p>
        </div>
        
        
    @if (method_exists($this, 'form'))
        <form wire:submit="save">
            {{ $this->form }}
        </form>
    @endif
    </div>
</x-filament-panels::page>