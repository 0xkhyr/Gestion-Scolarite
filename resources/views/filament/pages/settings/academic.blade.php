<x-filament-panels::page>
    <div class="border-b border-gray-200 pb-4">
        <p class="mt-1 text-sm text-gray-600">{{ __('app.academic_page_subtitle') }}</p>
    </div>
    @include('filament.components.settings-navigation')
    
    @if (method_exists($this, 'form'))
        <x-filament-panels::form wire:submit="save">
            {{ $this->form }}
        </x-filament-panels::form>
    @endif
</x-filament-panels::page>