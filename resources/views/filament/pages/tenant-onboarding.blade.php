<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Progress Indicator --}}
        <div class="rounded-xl bg-white/80 backdrop-blur-sm shadow-sm border border-gray-200/50 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Setup Progress</h3>
                <span class="text-sm font-medium text-commatix-600">
                    {{ $progress?->getCompletionPercentage() ?? 0 }}% Complete
                </span>
            </div>

            {{-- Progress bar --}}
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div
                    class="h-full bg-gradient-to-r from-commatix-500 to-commatix-600 rounded-full transition-all duration-500 ease-out"
                    style="width: {{ $progress?->getCompletionPercentage() ?? 0 }}%"
                ></div>
            </div>

            {{-- Step indicators --}}
            <div class="grid grid-cols-6 gap-2 mt-6">
                @for ($i = 1; $i <= 6; $i++)
                    @php
                        $stepField = "step_{$i}_completed";
                        $isCompleted = $progress?->$stepField ?? false;
                        $isCurrent = $progress?->current_step === $i;
                    @endphp
                    <div class="flex flex-col items-center text-center">
                        <div class="
                            w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold mb-2 transition-all
                            {{ $isCompleted ? 'bg-commatix-500 text-white' : ($isCurrent ? 'bg-commatix-100 text-commatix-700 ring-2 ring-commatix-500' : 'bg-gray-200 text-gray-500') }}
                        ">
                            @if ($isCompleted)
                                <x-heroicon-m-check class="w-5 h-5" />
                            @else
                                {{ $i }}
                            @endif
                        </div>
                        <span class="text-xs {{ $isCurrent ? 'font-semibold text-gray-900' : 'text-gray-600' }}">
                            Step {{ $i }}
                        </span>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Wizard Form --}}
        <div class="rounded-xl bg-white/80 backdrop-blur-sm shadow-sm border border-gray-200/50">
            {{ $this->form }}
        </div>

        {{-- Save & Exit Button --}}
        <div class="flex justify-end">
            <x-filament::button
                wire:click="saveAndExit"
                color="gray"
                outlined
            >
                Save & Exit
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
