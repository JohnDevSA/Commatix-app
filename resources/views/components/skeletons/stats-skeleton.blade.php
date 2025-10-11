{{-- Stats Skeleton Loader - Used for dashboard widgets --}}
@props(['count' => 4])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $count }} gap-4">
    @for($i = 0; $i < $count; $i++)
    <div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="space-y-3">
            {{-- Icon --}}
            <div class="h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>

            {{-- Label --}}
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>

            {{-- Value --}}
            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>

            {{-- Description/Change --}}
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
        </div>
    </div>
    @endfor
</div>
