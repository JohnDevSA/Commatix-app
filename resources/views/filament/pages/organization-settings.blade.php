<x-filament-panels::page>
    {{-- Page Header Enhancement --}}
    <div class="mb-6 animate-fade-in">
        <p class="text-sm text-commatix-600 dark:text-commatix-400">
            Manage your organization's profile, registration details, and contact information
        </p>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        {{-- Loading State --}}
        <div wire:loading wire:target="save" class="fixed inset-0 z-50 flex items-center justify-center bg-commatix-950/20 dark:bg-commatix-950/40 backdrop-blur-sm animate-fade-in">
            <div class="glass-card p-8 flex flex-col items-center gap-4 animate-slide-up">
                <svg class="w-12 h-12 text-commatix-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-medium text-commatix-900 dark:text-commatix-100">Saving changes...</p>
                <p class="text-xs text-commatix-600 dark:text-commatix-400">Please wait</p>
            </div>
        </div>

        {{-- South African UX Standard: Right-aligned form actions with proper spacing --}}
        <div class="flex items-center justify-end gap-6 mt-10 pt-6 border-t border-commatix-200 dark:border-commatix-700">
            {{-- Optional: Reset button --}}
            <x-filament::button
                type="button"
                color="gray"
                outlined
                wire:click="mount"
                icon="heroicon-o-arrow-path"
                class="hover:bg-commatix-50 dark:hover:bg-commatix-900 transition-colors min-w-[140px]"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">Reset Changes</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Resetting...
                </span>
            </x-filament::button>

            {{-- Primary action: Save --}}
            <x-filament::button
                type="submit"
                color="primary"
                icon="heroicon-o-check"
                class="shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95 min-w-[140px]"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">Save Changes</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                </span>
            </x-filament::button>
        </div>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>