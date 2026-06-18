<x-filament-panels::page>
    @if (method_exists($this, 'form'))
        <form wire:submit="save">
            {{ $this->form }}
            
            <x-filament::actions 
                :actions="$this->getFormActions()" 
                :full-width="false"
            />
        </form>
    @endif
</x-filament-panels::page>