<x-filament-panels::page>
    <div class="border-b border-gray-200 pb-4 dark:border-white/10">
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('app.modules_page_subtitle') }}</p>
    </div>
    @include('filament.components.settings-navigation')

    @if (method_exists($this, 'form'))
        <form wire:submit="save">
            {{ $this->form }}
        </form>
    @endif
</x-filament-panels::page>
