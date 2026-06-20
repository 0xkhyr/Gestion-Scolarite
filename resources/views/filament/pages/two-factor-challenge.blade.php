<form wire:submit="verify">
    {{ $this->form }}

    <x-filament::actions
        :actions="$this->getFormActions()"
        :full-width="true"
    />
</form>
