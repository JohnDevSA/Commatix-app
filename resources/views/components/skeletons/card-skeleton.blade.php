{{-- Card Skeleton Loader - Generic card skeleton --}}
@props(['rows' => 3])

<div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="space-y-4">
        {{-- Header --}}
        <div class="flex items-center space-x-4">
            <div class="h-12 w-12 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
            </div>
        </div>

        {{-- Content rows --}}
        @for($i = 0; $i < $rows; $i++)
        <div class="space-y-2">
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
        </div>
        @endfor
    </div>
</div>
