<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}
    </form>

    @php
        $isRtl = app()->getLocale() === 'ar';
    @endphp

    {{-- Class ranking --}}
    @if($this->id_classe && !$this->id_etudiant)
        @php
            $ranking = $this->classRanking;
        @endphp

        <x-filament::section icon="heroicon-o-trophy" :heading="__('app.classement_classe')" class="mt-6">
            <div class="overflow-x-auto" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="p-3 text-center font-semibold text-gray-500">{{ __('app.rang') }}</th>
                            <th class="p-3 text-start font-semibold text-gray-500">{{ __('app.etudiant') }}</th>
                            <th class="p-3 text-center font-semibold text-gray-500">{{ __('app.moyenne') }}</th>
                            <th class="p-3 text-center font-semibold text-gray-500">{{ __('app.mention') }}</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ranking as $index => $rank)
                            <tr class="border-b border-gray-100">
                                <td class="p-3 text-center">
                                    <x-filament::badge :color="$index === 0 ? 'warning' : 'gray'">
                                        {{ $index + 1 }}
                                    </x-filament::badge>
                                </td>
                                <td class="p-3">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $rank->nom_complet }}</span>
                                        <span class="font-mono text-xs text-gray-500">{{ $rank->matricule }}</span>
                                    </div>
                                </td>
                                <td class="p-3 text-center">
                                    <x-filament::badge :color="$rank->moyenne >= 10 ? 'success' : 'danger'">
                                        {{ number_format($rank->moyenne, 2) }}
                                    </x-filament::badge>
                                </td>
                                <td class="p-3 text-center">
                                    <x-filament::badge color="gray">{{ $rank->mention }}</x-filament::badge>
                                </td>
                                <td class="p-3 text-center">
                                    <x-filament::button
                                        wire:click="selectStudent({{ $rank->id_etudiant }})"
                                        size="xs"
                                        color="gray"
                                        icon="heroicon-o-eye"
                                    >
                                        {{ __('app.voir') }}
                                    </x-filament::button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif

    {{-- Student transcript --}}
    @if($this->id_etudiant)
        @php
            $etudiant = $this->etudiant;
            $notes = $this->notes;
            $moyenne = $this->moyenne;
            $hasNotes = $notes->count() > 0;
        @endphp

        <div class="mt-6 space-y-4" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
            {{-- Header --}}
            <x-filament::section>
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-primary-600 text-lg font-semibold text-white">
                        {{ \Illuminate\Support\Str::upper(substr($etudiant->nom ?? '', 0, 1) . substr($etudiant->prenom ?? '', 0, 1)) ?: '?' }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="truncate text-xl font-bold">
                            {{ $etudiant->nom }} {{ $etudiant->prenom }}
                        </h2>
                        <p class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                            <span>{{ __('app.matricule') }}: <span class="font-mono font-semibold text-primary-600">{{ $etudiant->matricule }}</span></span>
                            <span>•</span>
                            <span>{{ __('app.classe') }}: <span class="font-semibold">{{ $etudiant->classe->nom_classe ?? 'N/A' }}</span></span>
                        </p>
                    </div>
                </div>
            </x-filament::section>

            @if($hasNotes)
                {{-- Stats --}}
                <div class="flex flex-wrap gap-4">
                    <x-filament::section class="flex-1">
                        <div class="flex items-center gap-2 text-gray-500">
                            <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5" />
                            <span class="text-sm font-medium">{{ __('app.moyenne_generale') }}</span>
                        </div>
                        <div class="mt-2">
                            <x-filament::badge :color="$moyenne >= 10 ? 'success' : 'danger'" size="lg">
                                {{ number_format($moyenne, 2) }} / 20
                            </x-filament::badge>
                        </div>
                    </x-filament::section>

                    <x-filament::section class="flex-1">
                        <div class="flex items-center gap-2 text-gray-500">
                            <x-filament::icon icon="heroicon-o-star" class="h-5 w-5" />
                            <span class="text-sm font-medium">{{ __('app.mention') }}</span>
                        </div>
                        <div class="mt-2">
                            <x-filament::badge color="primary" size="lg">{{ $this->getMention($moyenne) }}</x-filament::badge>
                        </div>
                    </x-filament::section>

                    <x-filament::section class="flex-1">
                        <div class="flex items-center gap-2 text-gray-500">
                            <x-filament::icon icon="heroicon-o-clipboard-document-list" class="h-5 w-5" />
                            <span class="text-sm font-medium">{{ __('app.nombre_evaluations') }}</span>
                        </div>
                        <div class="mt-2 text-3xl font-bold">{{ $notes->count() }}</div>
                    </x-filament::section>
                </div>

                {{-- Grades table --}}
                <x-filament::section :heading="__('app.releve_notes') ?? __('app.note')">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="p-3 text-start font-semibold text-gray-500">{{ __('app.matiere') }}</th>
                                    <th class="p-3 text-start font-semibold text-gray-500">{{ __('app.evaluation') }}</th>
                                    <th class="p-3 text-center font-semibold text-gray-500">{{ __('app.note') }}</th>
                                    <th class="p-3 text-center font-semibold text-gray-500">{{ __('app.remarques') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notes as $note)
                                    <tr class="border-b border-gray-100">
                                        <td class="p-3 font-medium">{{ $note->evaluation->matiere->nom_matiere ?? 'N/A' }}</td>
                                        <td class="p-3 text-gray-500">{{ $note->evaluation->titre ?? __('app.evaluation') }}</td>
                                        <td class="p-3 text-center">
                                            <x-filament::badge :color="$note->note >= 10 ? 'success' : 'danger'">
                                                {{ number_format($note->note, 2) }} / 20
                                            </x-filament::badge>
                                        </td>
                                        <td class="p-3 text-center text-gray-500">{{ $note->commentaire ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @else
                {{-- No grades: neutral empty state --}}
                <x-filament::section>
                    <div class="flex flex-col items-center gap-3 py-12 text-center">
                        <x-filament::icon icon="heroicon-o-clipboard-document-list" class="h-10 w-10 text-gray-400" />
                        <div>
                            <p class="font-semibold">{{ __('app.no_notes_found') }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ __('app.no_notes_hint') }}</p>
                        </div>
                    </div>
                </x-filament::section>
            @endif
        </div>
    @endif
</x-filament-panels::page>
