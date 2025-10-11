<x-filament-panels::page>
    {{-- Show skeleton while loading --}}
    <div wire:loading.delay>
        <x-skeletons.table-skeleton />
    </div>

    {{-- Show actual table when loaded --}}
    <div wire:loading.remove.delay class="fade-in">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
