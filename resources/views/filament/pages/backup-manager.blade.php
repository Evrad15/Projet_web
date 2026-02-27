<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4">
        <x-filament::section>
            <x-slot name="heading">Dernières sauvegardes sur le disque</x-slot>
            
            <div class="space-y-2">
                @foreach($this->getBackups() as $backup)
                    <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-800 rounded-lg border">
                        <span class="font-mono text-sm">{{ basename($backup) }}</span>
                        <div class="flex gap-2">
                            {{-- On affiche juste le nom pour l'instant --}}
                            <span class="text-xs text-gray-500">Stocké dans storage/app/{{ $backup }}</span>
                        </div>
                    </div>
                @endforeach

                @if(empty($this->getBackups()))
                    <p class="text-gray-500 italic">Aucune sauvegarde trouvée pour le moment.</p>
                @endif
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>