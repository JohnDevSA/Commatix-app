@extends('layouts.onboarding-modern')

@section('title', 'Integrations')

@section('content')
<div x-data="step4Modern()" x-init="init()" class="slide-enter">
    <form @submit.prevent="handleSubmit">
        @csrf

        <!-- Question Container -->
        <div class="question-card p-8 md:p-12 mb-8">
            <!-- Skip Indicator -->
            <div class="text-sm font-semibold text-gray-500 mb-4">
                Step 4 of 6 • Optional
            </div>

            <!-- Main Question -->
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Do you use any of these tools?
                </h2>
                <p class="text-lg text-gray-600 mb-8">
                    We can integrate with your existing tools (completely optional)
                </p>

                <!-- Popular Integrations -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    @foreach([
                        'google_workspace' => [
                            'name' => 'Google Workspace',
                            'description' => 'Gmail, Calendar, Drive',
                            'icon' => '🏢',
                            'color' => 'blue'
                        ],
                        'microsoft_365' => [
                            'name' => 'Microsoft 365',
                            'description' => 'Outlook, Teams, OneDrive',
                            'icon' => '📧',
                            'color' => 'indigo'
                        ],
                        'slack' => [
                            'name' => 'Slack',
                            'description' => 'Team communication',
                            'icon' => '💬',
                            'color' => 'purple'
                        ],
                        'xero' => [
                            'name' => 'Xero',
                            'description' => 'Accounting software',
                            'icon' => '💰',
                            'color' => 'green'
                        ],
                        'sage' => [
                            'name' => 'Sage',
                            'description' => 'Business accounting',
                            'icon' => '📊',
                            'color' => 'emerald'
                        ],
                        'zapier' => [
                            'name' => 'Zapier',
                            'description' => 'Connect 1000+ apps',
                            'icon' => '⚡',
                            'color' => 'orange'
                        ],
                    ] as $key => $integration)
                        <label class="option-card cursor-pointer"
                               :class="{ 'selected': selectedIntegrations.includes('{{ $key }}') }">
                            <input
                                type="checkbox"
                                name="integrations[]"
                                value="{{ $key }}"
                                x-model="selectedIntegrations"
                                class="sr-only"
                            />
                            <div class="flex items-center space-x-4">
                                <div class="text-4xl flex-shrink-0">{{ $integration['icon'] }}</div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-lg font-semibold text-gray-900">{{ $integration['name'] }}</div>
                                    <div class="text-sm text-gray-600">{{ $integration['description'] }}</div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div x-show="selectedIntegrations.includes('{{ $key }}')"
                                         class="w-6 h-6 bg-{{ $integration['color'] }}-600 rounded-md flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div x-show="!selectedIntegrations.includes('{{ $key }}')"
                                         class="w-6 h-6 border-2 border-gray-300 rounded-md"></div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <!-- Selected Count -->
                <div x-show="selectedIntegrations.length > 0" x-transition class="mb-6 p-4 bg-green-50 rounded-xl border border-green-200">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-green-800 font-medium">
                            <span x-text="selectedIntegrations.length"></span> integration<span x-show="selectedIntegrations.length !== 1">s</span> selected
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-green-700">
                        We'll help you connect these after setup is complete
                    </p>
                </div>

                <!-- Skip Info -->
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-gray-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-gray-700">
                            <strong>Don't see your tool?</strong> No problem! You can set up integrations later from your dashboard, or skip this step entirely.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('onboarding.step', 3) }}"
               class="btn-secondary-modern inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>

            <div class="flex-1"></div>

            <button
                type="submit"
                class="btn-primary-modern inline-flex items-center">
                <span x-show="selectedIntegrations.length === 0">Skip Integrations</span>
                <span x-show="selectedIntegrations.length > 0">Continue</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <input type="hidden" name="action" value="next">
    </form>
</div>
@endsection

@push('scripts')
<script>
function step4Modern() {
    return {
        selectedIntegrations: @json(old('integrations', $stepData['integrations'] ?? [])),

        init() {
            document.addEventListener('keydown', (e) => {
                // Alt + Right Arrow = Skip/Continue
                if (e.altKey && e.key === 'ArrowRight') {
                    e.preventDefault();
                    this.$el.querySelector('form').submit();
                }
            });
        },

        handleSubmit(event) {
            event.target.submit();
        }
    }
}
</script>
@endpush
