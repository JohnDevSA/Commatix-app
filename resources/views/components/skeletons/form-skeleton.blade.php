{{-- Form Skeleton Loader - Used for create/edit forms --}}
<div class="animate-pulse space-y-6">
    {{-- Form sections --}}
    @for($section = 0; $section < 2; $section++)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        {{-- Section title --}}
        <div class="mb-4">
            <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mt-2"></div>
        </div>

        {{-- Form fields --}}
        <div class="grid grid-cols-2 gap-4">
            @for($field = 0; $field < 4; $field++)
            <div class="space-y-2">
                {{-- Label --}}
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>

                {{-- Input field --}}
                <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>

                {{-- Helper text --}}
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
            </div>
            @endfor
        </div>
    </div>
    @endfor

    {{-- Action buttons --}}
    <div class="flex justify-end space-x-2">
        <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded w-24"></div>
        <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
    </div>
</div>
