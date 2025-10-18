@props([
    'target' => 'save',
    'message' => 'Processing...',
    'description' => 'Please wait'
])

{{-- Loading Overlay with Glassmorphic Design --}}
<div
    wire:loading
    wire:target="{{ $target }}"
    class="fixed inset-0 z-50 flex items-center justify-center bg-commatix-950/20 dark:bg-commatix-950/40 backdrop-blur-sm animate-fade-in"
    role="status"
    aria-live="polite"
    aria-label="{{ $message }}"
>
    <div class="glass-card p-8 flex flex-col items-center gap-4 animate-slide-up">
        {{-- Spinner --}}
        <svg class="w-12 h-12 text-commatix-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        {{-- Message --}}
        <p class="text-sm font-medium text-commatix-900 dark:text-commatix-100">{{ $message }}</p>

        {{-- Description --}}
        @if($description)
            <p class="text-xs text-commatix-600 dark:text-commatix-400">{{ $description }}</p>
        @endif
    </div>
</div>
