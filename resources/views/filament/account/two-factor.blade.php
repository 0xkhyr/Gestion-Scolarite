<x-filament::section
    icon="heroicon-o-shield-check"
    :heading="__('app.two_factor_authentication')"
    :description="__('app.two_factor_authentication_desc')"
>
    @if ($this->isTwoFactorEnabled())
        {{-- ───────────── Enabled state ───────────── --}}
        <div class="flex flex-col gap-5">
            <div class="flex items-center gap-3 rounded-xl bg-success-50 p-4 ring-1 ring-inset ring-success-600/20 dark:bg-success-400/10 dark:ring-success-400/30">
                <x-filament::icon icon="heroicon-o-shield-check" class="h-7 w-7 shrink-0 text-success-600 dark:text-success-400" />
                <div>
                    <p class="text-sm font-semibold text-success-800 dark:text-success-300">{{ __('app.2fa_enabled_title') }}</p>
                    <p class="text-xs text-success-700 dark:text-success-400/80">{{ __('app.2fa_enabled_body') }}</p>
                </div>
            </div>

            @if (count($this->getRecoveryCodes()))
                <div class="rounded-xl border border-warning-300 bg-warning-50 p-4 dark:border-warning-400/20 dark:bg-warning-400/10">
                    <p class="mb-1 text-sm font-semibold text-warning-800 dark:text-warning-300">{{ __('app.recovery_codes') }}</p>
                    <p class="mb-3 text-xs text-warning-700 dark:text-warning-400/80">{{ __('app.recovery_codes_helper') }}</p>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach ($this->getRecoveryCodes() as $code)
                            <code class="rounded-md border border-warning-200 bg-white px-2 py-1 text-center font-mono text-xs tracking-wider text-gray-800 dark:border-warning-400/20 dark:bg-gray-900 dark:text-gray-200">{{ $code }}</code>
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <x-filament::button
                    wire:click="mountAction('disable2fa')"
                    color="danger"
                    icon="heroicon-m-shield-exclamation"
                >
                    {{ __('app.disable_2fa') }}
                </x-filament::button>
            </div>
        </div>
    @else
        {{-- ───────────── Setup (disabled) state ───────────── --}}
        <div
            x-data="{ copied: false, secret: @js($this->getSecretKey()) }"
            class="flex flex-col gap-6"
        >
            {{-- Prominent status banner --}}
            <div class="flex items-center gap-3 rounded-xl bg-danger-50 p-4 ring-1 ring-inset ring-danger-600/20 dark:bg-danger-400/10 dark:ring-danger-400/30">
                <x-filament::icon icon="heroicon-o-shield-exclamation" class="h-7 w-7 shrink-0 text-danger-600 dark:text-danger-400" />
                <div>
                    <p class="text-sm font-semibold text-danger-800 dark:text-danger-300">{{ __('app.twofa_off_title') }}</p>
                    <p class="text-xs text-danger-700 dark:text-danger-400/80">{{ __('app.twofa_off_body') }}</p>
                </div>
            </div>

            {{-- Step 1: scan QR --}}
            <div class="flex gap-4">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-semibold text-white">1</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-950 dark:text-white">{{ __('app.scan_qr_help') }}</p>
                    <div class="mt-3 flex justify-center">
                        <div class="w-fit rounded-xl bg-white p-3 ring-1 ring-gray-950/10">
                            {!! $this->getQrCodeHtml() !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: secret key + copy --}}
            <div class="flex gap-4">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-semibold text-white">2</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-950 dark:text-white">{{ __('app.manual_entry_helper') }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        <code
                            x-text="secret"
                            class="min-w-0 flex-1 break-all rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 font-mono text-sm tracking-[0.2em] text-gray-800 dark:border-white/10 dark:bg-white/5 dark:text-gray-100"
                        ></code>
                        <x-filament::icon-button
                            icon="heroicon-m-clipboard-document"
                            :label="__('app.copy')"
                            x-on:click="navigator.clipboard.writeText(secret); copied = true; setTimeout(() => copied = false, 1500)"
                        />
                        <span x-show="copied" x-transition class="whitespace-nowrap text-xs font-medium text-success-600 dark:text-success-400">{{ __('app.copied') }}</span>
                    </div>
                </div>
            </div>

            {{-- Step 3: verify code + enable --}}
            <div class="flex gap-4">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-semibold text-white">3</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-950 dark:text-white">{{ __('app.enter_code_from_app') }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <x-filament::input.wrapper class="w-36">
                            <x-filament::input
                                type="text"
                                wire:model="two_factor_code"
                                wire:keydown.enter="enable2FA"
                                placeholder="123456"
                                maxlength="6"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                dir="ltr"
                            />
                        </x-filament::input.wrapper>
                        <x-filament::button
                            wire:click="enable2FA"
                            icon="heroicon-m-shield-check"
                            color="success"
                        >
                            {{ __('app.enable_2fa') }}
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament::section>
