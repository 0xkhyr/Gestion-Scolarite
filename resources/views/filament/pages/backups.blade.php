<x-filament-panels::page>
    {{-- Encryption status (the password itself is never shown — it lives only in
         the server secret BACKUP_ARCHIVE_PASSWORD). --}}
    @if ($this->isEncrypted())
        <div class="fi-section rounded-xl bg-success-50 p-4 ring-1 ring-success-600/20 dark:bg-success-500/10 dark:ring-success-400/20">
            <div class="flex items-start gap-2">
                <x-filament::icon icon="heroicon-m-lock-closed" class="mt-0.5 h-5 w-5 text-success-600 dark:text-success-400" />
                <div>
                    <p class="text-sm font-semibold text-success-900 dark:text-success-200">{{ __('app.backups_encrypted') }}</p>
                    <p class="mt-0.5 text-xs text-success-800/80 dark:text-success-200/70">{{ __('app.backups_encrypted_help') }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="fi-section rounded-xl bg-danger-50 p-4 ring-1 ring-danger-600/20 dark:bg-danger-500/10 dark:ring-danger-400/20">
            <div class="flex items-start gap-2">
                <x-filament::icon icon="heroicon-m-lock-open" class="mt-0.5 h-5 w-5 text-danger-600 dark:text-danger-400" />
                <div>
                    <p class="text-sm font-semibold text-danger-900 dark:text-danger-200">{{ __('app.backups_not_encrypted') }}</p>
                    <p class="mt-0.5 text-xs text-danger-800/80 dark:text-danger-200/70">{{ __('app.backups_not_encrypted_help') }}</p>
                </div>
            </div>
        </div>
    @endif

    @php($backups = $this->getBackups())

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="px-4 py-3 text-start font-medium text-gray-700 dark:text-gray-200">{{ __('app.backup_file') }}</th>
                        <th class="px-4 py-3 text-start font-medium text-gray-700 dark:text-gray-200">{{ __('app.date') }}</th>
                        <th class="px-4 py-3 text-start font-medium text-gray-700 dark:text-gray-200">{{ __('app.backup_size') }}</th>
                        <th class="px-4 py-3 text-start font-medium text-gray-700 dark:text-gray-200">{{ __('app.disk') }}</th>
                        <th class="px-4 py-3 text-end font-medium text-gray-700 dark:text-gray-200">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($backups as $backup)
                        <tr class="border-b border-gray-100 dark:border-white/5">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $backup['name'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                {{ \Illuminate\Support\Carbon::createFromTimestamp($backup['timestamp'])->translatedFormat('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $this->formatSize($backup['size']) }}</td>
                            <td class="px-4 py-3">
                                <span class="fi-badge inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 dark:bg-white/10 dark:text-gray-200">
                                    {{ $backup['disk'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <x-filament::button
                                        tag="a"
                                        size="sm"
                                        color="gray"
                                        icon="heroicon-m-arrow-down-tray"
                                        :href="$backup['download_url']"
                                        target="_blank"
                                        download
                                        wire:navigate.ignore>
                                        {{ __('app.download') }}
                                    </x-filament::button>
                                    <x-filament::button
                                        size="sm"
                                        color="danger"
                                        icon="heroicon-m-trash"
                                        wire:click="mountAction('deleteBackup', { disk: '{{ $backup['disk'] }}', file: '{{ $backup['name'] }}' })">
                                        {{ __('app.delete') }}
                                    </x-filament::button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                {{ __('app.no_backups') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
