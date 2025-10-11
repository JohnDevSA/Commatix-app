<x-filament-panels::page>
    {{-- Show skeleton while loading --}}
    <div wire:loading.delay class="space-y-6">
        {{-- Stats skeleton --}}
        <x-skeletons.stats-skeleton :count="6" />

        {{-- Charts skeleton --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-skeletons.chart-skeleton />
            <x-skeletons.chart-skeleton />
        </div>
    </div>

    {{-- Show actual widgets when loaded --}}
    <div wire:loading.remove.delay class="fade-in">
        <x-filament-widgets::widgets
            :widgets="$this->getVisibleWidgets()"
            :columns="$this->getColumns()"
        />
    </div>
</x-filament-panels::page>
