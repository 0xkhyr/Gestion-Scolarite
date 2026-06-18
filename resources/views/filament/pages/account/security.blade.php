<x-filament-panels::page>
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-4 dark:border-white/10">
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('app.security_page_subtitle') }}</p>
        </div>

        @include('filament.components.account-navigation')

        @include('filament.account.two-factor')

        @if (method_exists($this, 'form'))
            <form wire:submit="save">
                {{ $this->form }}
            </form>
        @endif
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
