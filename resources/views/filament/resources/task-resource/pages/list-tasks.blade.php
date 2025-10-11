<x-filament-panels::page>
    <div
        x-data="{ showSkeleton: true }"
        x-init="
            console.log('Alpine initialized - showing skeleton');
            setTimeout(() => {
                console.log('Hiding skeleton after 2 seconds');
                showSkeleton = false;
            }, 2000);
        "
    >
        {{-- Skeleton loader - shown initially for 2 seconds --}}
        <div x-show="showSkeleton">
            <x-skeletons.table-skeleton />
        </div>

        {{-- Actual content - shown after skeleton --}}
        <div x-show="!showSkeleton">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
