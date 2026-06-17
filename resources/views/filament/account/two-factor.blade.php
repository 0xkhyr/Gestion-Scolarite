<x-filament::section
    icon="heroicon-o-shield-check"
    :heading="__('app.two_factor_authentication')"
    :description="__('app.two_factor_authentication_desc')"
>
    @if ($this->isTwoFactorEnabled())
        {{-- Enabled state --}}
        <div class="space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <x-filament::badge color="success" size="lg" icon="heroicon-m-shield-check">
                    {{ __('app.enabled') }}
                </x-filament::badge>
                <span class="text-sm text-gray-500">{{ __('app.2fa_enabled_body') }}</span>
            </div>

            @if (count($this->getRecoveryCodes()))
                <div>
                    <p class="text-sm font-medium">{{ __('app.recovery_codes') }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ __('app.recovery_codes_helper') }}</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($this->getRecoveryCodes() as $code)
                            <span class="rounded-md bg-gray-100 px-2 py-1 font-mono text-xs text-gray-700">{{ $code }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <x-filament::button
                wire:click="mountAction('disable2fa')"
                color="danger"
                icon="heroicon-m-shield-exclamation"
            >
                {{ __('app.disable_2fa') }}
            </x-filament::button>
        </div>
    @else
        {{-- Setup state --}}
        <div x-data="{ copied: false, secret: @js($this->getSecretKey()) }" class="space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <x-filament::badge color="danger" size="lg" icon="heroicon-m-shield-exclamation">
                    {{ __('app.disabled') }}
                </x-filament::badge>
                <span class="text-sm text-gray-500">{{ __('app.twofa_off_body') }}</span>
            </div>

            {{-- Step 1: scan QR --}}
            <div class="flex gap-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-semibold text-white">1</span>
                <div class="flex-1">
                    <p class="text-sm font-medium">{{ __('app.scan_qr_help') }}</p>
                    <div class="mt-2 inline-block rounded-lg bg-white p-3">
                        {!! $this->getQrCodeHtml() !!}
                    </div>
                </div>
            </div>

            {{-- Step 2: secret key + copy --}}
            <div class="flex gap-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-semibold text-white">2</span>
                <div class="flex-1">
                    <p class="text-sm font-medium">{{ __('app.manual_entry_helper') }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <code class="rounded-md bg-gray-100 px-3 py-1 font-mono text-sm text-gray-700" x-text="secret"></code>
                        <x-filament::icon-button
                            icon="heroicon-m-clipboard-document"
                            :label="__('app.copy')"
                            x-on:click="navigator.clipboard.writeText(secret); copied = true; setTimeout(() => copied = false, 1500)"
                        />
                        <span x-show="copied" x-transition class="text-xs text-gray-500">{{ __('app.copied') }}</span>
                    </div>
                </div>
            </div>

            {{-- Step 3: verify code + enable --}}
            <div class="flex gap-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-semibold text-white">3</span>
                <div class="flex-1">
                    <p class="text-sm font-medium">{{ __('app.enter_code_from_app') }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <div class="max-w-xs">
                            <x-filament::input.wrapper>
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
                        </div>
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
