{{-- Table Skeleton Loader - Used for resource tables --}}
<div class="animate-pulse space-y-4">
    {{-- Header with search and actions --}}
    <div class="flex justify-between items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
        <div class="flex space-x-2">
            <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
            <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
        </div>
    </div>

    {{-- Table rows --}}
    <div class="space-y-3">
        @for($i = 0; $i < 8; $i++)
        <div class="flex space-x-4 items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
            {{-- Avatar/Icon --}}
            <div class="h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded-full"></div>

            {{-- Content --}}
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
            </div>

            {{-- Status badges --}}
            <div class="flex space-x-2">
                <div class="h-6 w-16 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                <div class="h-6 w-16 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
            </div>

            {{-- Actions --}}
            <div class="flex space-x-2">
                <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </div>
        </div>
        @endfor
    </div>

    {{-- Pagination --}}
    <div class="flex justify-between items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
        <div class="flex space-x-2">
            <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
            <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
            <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
        </div>
    </div>
</div>
