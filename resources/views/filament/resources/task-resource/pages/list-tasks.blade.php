<x-filament-panels::page>
    {{-- Skeleton loader - shown while table is loading --}}
    <div wire:loading wire:target="previousPage,nextPage,gotoPage,sortTable,tableFilters,tableSearch,tableColumnSearches" class="mb-4">
        <x-skeletons.table-skeleton />
    </div>

    {{-- Actual content --}}
    <div wire:loading.remove wire:target="previousPage,nextPage,gotoPage,sortTable,tableFilters,tableSearch,tableColumnSearches">
        {{ $this->table }}
    </div>

    {{-- Initial load skeleton (JavaScript-based) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // This script runs on first page load
            const table = document.querySelector('[wire\\:loading\\.remove]');
            if (table && !table.querySelector('table')) {
                // Table hasn't loaded yet, show skeleton temporarily
                table.style.display = 'none';
                const skeleton = document.querySelector('[wire\\:loading]');
                if (skeleton) {
                    skeleton.style.display = 'block';
                    setTimeout(() => {
                        skeleton.style.display = 'none';
                        table.style.display = 'block';
                    }, 1000);
                }
            }
        });
    </script>
</x-filament-panels::page>
