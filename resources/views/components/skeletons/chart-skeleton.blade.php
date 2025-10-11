{{-- Chart Skeleton Loader - Used for dashboard charts --}}
<div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="space-y-4">
        {{-- Chart title --}}
        <div class="flex justify-between items-center">
            <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
        </div>

        {{-- Chart legend --}}
        <div class="flex space-x-4">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
        </div>

        {{-- Chart area --}}
        <div class="relative h-64">
            {{-- Vertical bars simulation --}}
            <div class="absolute bottom-0 left-0 right-0 flex items-end justify-around space-x-2 h-full">
                @for($i = 0; $i < 12; $i++)
                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-t"
                     style="height: {{ rand(30, 100) }}%;"></div>
                @endfor
            </div>
        </div>

        {{-- X-axis labels --}}
        <div class="flex justify-between">
            @for($i = 0; $i < 6; $i++)
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-12"></div>
            @endfor
        </div>
    </div>
</div>
