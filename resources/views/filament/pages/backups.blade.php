<x-filament-panels::page>
    {{-- Encryption status (the password itself is never shown — it lives only in
         the server secret BACKUP_ARCHIVE_PASSWORD). --}}
    <x-filament::section>
        @if ($this->isEncrypted())
            <div class="flex flex-wrap items-center gap-2">
                <x-filament::badge color="success" size="lg" icon="heroicon-m-lock-closed">
                    {{ __('app.backups_encrypted') }}
                </x-filament::badge>
                <span class="text-sm text-gray-500">{{ __('app.backups_encrypted_help') }}</span>
            </div>
        @else
            <div class="flex flex-wrap items-center gap-2">
                <x-filament::badge color="danger" size="lg" icon="heroicon-m-lock-open">
                    {{ __('app.backups_not_encrypted') }}
                </x-filament::badge>
                <span class="text-sm text-gray-500">{{ __('app.backups_not_encrypted_help') }}</span>
            </div>
        @endif
    </x-filament::section>

    @php($backups = $this->getBackups())

    <x-filament::section>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="p-3 text-start font-semibold text-gray-500">{{ __('app.backup_file') }}</th>
                        <th class="p-3 text-start font-semibold text-gray-500">{{ __('app.date') }}</th>
                        <th class="p-3 text-start font-semibold text-gray-500">{{ __('app.backup_size') }}</th>
                        <th class="p-3 text-start font-semibold text-gray-500">{{ __('app.disk') }}</th>
                        <th class="p-3 text-end font-semibold text-gray-500">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($backups as $backup)
                        <tr class="border-b border-gray-100">
                            <td class="p-3 font-medium">{{ $backup['name'] }}</td>
                            <td class="p-3 text-gray-500">
                                {{ \Illuminate\Support\Carbon::createFromTimestamp($backup['timestamp'])->translatedFormat('d M Y H:i') }}
                            </td>
                            <td class="p-3 text-gray-500">{{ $this->formatSize($backup['size']) }}</td>
                            <td class="p-3">
                                <x-filament::badge color="gray">{{ $backup['disk'] }}</x-filament::badge>
                            </td>
                            <td class="p-3">
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
                            <td colspan="5" class="p-6 text-center text-gray-500">
                                {{ __('app.no_backups') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-panels::page>
