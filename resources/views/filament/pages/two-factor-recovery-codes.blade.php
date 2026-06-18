<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-start justify-between max-w-4xl mx-auto">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('app.two_factor_recovery_codes') }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('app.codes_recuperation_helper') }}</p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Keep actions visible below; we don't duplicate server actions here to avoid changing behavior --}}
            </div>
        </div>

        {{-- Warning banner (compact) --}}
        <div class="max-w-4xl mx-auto">
            <div class="rounded-xl border border-warning-200 dark:border-warning-700 bg-warning-50 dark:bg-warning-900/20 p-4">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-0.5 text-warning-600 dark:text-warning-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-warning-900 dark:text-warning-100">{{ __('app.important_notice') }}</p>
                        <p class="text-sm text-warning-800 dark:text-warning-200 mt-1">{{ __('app.recovery_codes_warning') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Password form (only before showing codes) --}}
        @if (! $this->showCodes)
            <div class="max-w-2xl mx-auto">
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
                    <form wire:submit="viewCodes">
                        {{ $this->form }}

                        <div class="mt-4">
                            <x-filament::actions :actions="$this->getFormActions()" />
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Codes --}}
        @if ($this->showCodes)
            <div class="space-y-6 max-w-4xl mx-auto">
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('app.your_recovery_codes') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('app.codes_recuperation_helper') }}</p>
                        </div>

                        {{-- Secondary hint / small action area (download/copy are handled by server actions below) --}}
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.keep_codes_safe') }}</div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach ($this->recoveryCodes as $code)
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 p-3.5 transition-colors hover:bg-gray-100 dark:hover:bg-gray-900 flex items-center justify-between gap-3">
                                <div class="font-mono text-sm font-semibold tracking-wider text-gray-900 dark:text-gray-100 select-all truncate">
                                    {{ $code }}
                                </div>

                                <div class="flex-shrink-0">
                                    <button type="button" class="copy-code-btn inline-flex items-center gap-2 px-2 py-1 rounded text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-900" data-code="{{ $code }}" aria-label="{{ __('app.copy') }}">
                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8M8 8h8" />
                                        </svg>
                                        <span class="sr-only">{{ __('app.copy') }}</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <x-filament::actions :actions="$this->getFormActions()" />
            </div>
        @endif
    </div>

    {{-- Clipboard handler --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('copy-to-clipboard', async ({ text }) => {
                try {
                    await navigator.clipboard.writeText(text);
                } catch (e) {
                    console.error(e);
                    alert('Impossible de copier automatiquement. Copiez manuellement.');
                }
            });

            // per-code copy buttons
            document.addEventListener('click', async (e) => {
                const btn = e.target.closest('.copy-code-btn');
                if (!btn) return;

                const code = btn.getAttribute('data-code') || '';
                if (!code) return;

                try {
                    await navigator.clipboard.writeText(code);
                    // small feedback — use alert as fallback
                    try {
                        // Prefer a small non-blocking feedback if Filament exposes a toast method
                        if (window.Filament && typeof window.Filament.notify === 'function') {
                            Filament.notify({ type: 'success', message: '{{ addslashes(__('app.codes_copied') ?: 'Code copié ✅') }}' });
                        } else {
                            // gentle fallback
                            const el = document.createElement('div');
                            el.textContent = '{{ addslashes(__('app.codes_copied') ?: 'Code copié ✅') }}';
                            el.className = 'fixed bottom-4 right-4 bg-green-600 text-white text-sm px-3 py-2 rounded shadow';
                            document.body.appendChild(el);
                            setTimeout(() => el.remove(), 1800);
                        }
                    } catch (err) {
                        // ignore silently
                    }
                } catch (err) {
                    console.error(err);
                    alert('Impossible de copier automatiquement. Copiez manuellement.');
                }
            });
        });
    </script>
</x-filament-panels::page>
