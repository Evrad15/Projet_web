<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4">
        <x-filament::section>
            <x-slot name="heading">Dernieres sauvegardes sur le disque</x-slot>

            <div class="space-y-2">
                @foreach($this->getBackups() as $backup)
                    <div class="flex items-center gap-3 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg border">
                        <div class="min-w-0">
                            <span class="font-mono text-sm block">{{ basename($backup) }}</span>
                            <span class="text-xs text-gray-500 block">Stocke dans storage/app/{{ $backup }}</span>
                        </div>
                        <div class="ml-auto">
                            <button
                                type="button"
                                wire:click="deleteBackup('{{ base64_encode($backup) }}')"
                                wire:confirm="Supprimer cette sauvegarde ?"
                                class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-semibold"
                                style="background-color:#dc2626 !important;color:#ffffff !important;border:0 !important;"
                                onmouseover="this.style.backgroundColor='#b91c1c'"
                                onmouseout="this.style.backgroundColor='#dc2626'"
                            >
                                Supprimer
                            </button>
                        </div>
                    </div>
                @endforeach

                @if(empty($this->getBackups()))
                    <p class="text-gray-500 italic">Aucune sauvegarde trouvee pour le moment.</p>
                @endif
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
