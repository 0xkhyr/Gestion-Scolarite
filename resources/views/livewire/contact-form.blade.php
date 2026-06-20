<div>
    <!-- Success Message -->
    @if($showSuccess)
        <div class="mb-6 flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            <p class="text-sm text-emerald-800">{{ __('Thank you for your message! We will get back to you soon.') }}</p>
        </div>
    @endif

    <!-- Error Message -->
    @if($showError)
        <div class="mb-6 flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
            <span class="h-2 w-2 rounded-full bg-red-500"></span>
            <p class="text-sm text-red-800">{{ __('Sorry, there was an error sending your message. Please try again later.') }}</p>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-5">
        <div class="grid sm:grid-cols-2 gap-5">
            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-zinc-700 mb-1.5">
                    {{ __('Full name') }} <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       wire:model.live.debounce.300ms="name"
                       class="w-full px-3.5 py-2.5 text-sm border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-primary-500 transition-colors duration-150
                              @error('name') border-red-300 focus:ring-red-100 focus:border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-zinc-700 mb-1.5">
                    {{ __('Email') }} <span class="text-red-500">*</span>
                </label>
                <input type="email"
                       id="email"
                       wire:model.live.debounce.300ms="email"
                       class="w-full px-3.5 py-2.5 text-sm border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-primary-500 transition-colors duration-150
                              @error('email') border-red-300 focus:ring-red-100 focus:border-red-500 @enderror"
                       required>
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <!-- Phone Field -->
            <div>
                <label for="phone" class="block text-sm font-medium text-zinc-700 mb-1.5">
                    {{ __('Phone') }} <span class="text-zinc-400 font-normal">({{ __('optional') }})</span>
                </label>
                <input type="tel"
                       id="phone"
                       wire:model.live.debounce.300ms="phone"
                       class="w-full px-3.5 py-2.5 text-sm border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-primary-500 transition-colors duration-150
                              @error('phone') border-red-300 focus:ring-red-100 focus:border-red-500 @enderror">
                @error('phone')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject Field -->
            <div>
                <label for="subject" class="block text-sm font-medium text-zinc-700 mb-1.5">
                    {{ __('Subject') }} <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="subject"
                       wire:model.live.debounce.300ms="subject"
                       class="w-full px-3.5 py-2.5 text-sm border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-primary-500 transition-colors duration-150
                              @error('subject') border-red-300 focus:ring-red-100 focus:border-red-500 @enderror"
                       required>
                @error('subject')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Message Field -->
        <div>
            <label for="message" class="block text-sm font-medium text-zinc-700 mb-1.5">
                {{ __('Message') }} <span class="text-red-500">*</span>
            </label>
            <textarea id="message"
                      wire:model.live.debounce.300ms="message"
                      rows="5"
                      class="w-full px-3.5 py-2.5 text-sm border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-primary-500 transition-colors duration-150 resize-none
                             @error('message') border-red-300 focus:ring-red-100 focus:border-red-500 @enderror"
                      required></textarea>
            @error('message')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit"
                wire:loading.attr="disabled"
                class="w-full sm:w-auto bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="submit">{{ __('Send message') }}</span>
            <span wire:loading wire:target="submit">{{ __('Sending...') }}</span>
        </button>
    </form>
</div>
