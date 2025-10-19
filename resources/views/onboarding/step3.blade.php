@extends('layouts.onboarding')

@section('title', 'Primary Use Case')

@section('content')
<div x-data="step3Form()" x-init="init()">
    <div class="mb-10">
        <h2 class="text-4xl font-bold text-gray-900 mb-3">What do you want to achieve?</h2>
        <p class="text-lg text-gray-600">Help us tailor Commatix to your primary business needs</p>
    </div>

    <form method="POST" action="{{ route('onboarding.process', 3) }}" @submit="handleSubmit">
        @csrf
        <input type="hidden" name="action" x-model="action">

        <!-- Primary Use Case Selection -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Select your primary use case</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Email Marketing -->
                <label class="cursor-pointer">
                    <input type="radio" name="use_case" value="email_marketing"
                           x-model="useCase" required
                           {{ old('use_case', $stepData['use_case'] ?? '') == 'email_marketing' ? 'checked' : '' }}
                           class="sr-only">
                    <div class="glass-input p-6 rounded-xl transition-all duration-200"
                         :class="useCase === 'email_marketing' ? 'ring-2 ring-commatix-500 bg-commatix-50' : 'hover:bg-white'">
                        <div class="flex items-start">
                            <svg class="w-8 h-8 text-commatix-600 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-1">Email Marketing</h4>
                                <p class="text-sm text-gray-600">Build and manage email campaigns, newsletters, and subscriber lists</p>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- SMS Campaigns -->
                <label class="cursor-pointer">
                    <input type="radio" name="use_case" value="sms_campaigns"
                           x-model="useCase" required
                           {{ old('use_case', $stepData['use_case'] ?? '') == 'sms_campaigns' ? 'checked' : '' }}
                           class="sr-only">
                    <div class="glass-input p-6 rounded-xl transition-all duration-200"
                         :class="useCase === 'sms_campaigns' ? 'ring-2 ring-commatix-500 bg-commatix-50' : 'hover:bg-white'">
                        <div class="flex items-start">
                            <svg class="w-8 h-8 text-commatix-600 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-1">SMS Campaigns</h4>
                                <p class="text-sm text-gray-600">Send bulk SMS messages and manage mobile marketing campaigns</p>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- Workflow Automation -->
                <label class="cursor-pointer">
                    <input type="radio" name="use_case" value="workflow_automation"
                           x-model="useCase" required
                           {{ old('use_case', $stepData['use_case'] ?? '') == 'workflow_automation' ? 'checked' : '' }}
                           class="sr-only">
                    <div class="glass-input p-6 rounded-xl transition-all duration-200"
                         :class="useCase === 'workflow_automation' ? 'ring-2 ring-commatix-500 bg-commatix-50' : 'hover:bg-white'">
                        <div class="flex items-start">
                            <svg class="w-8 h-8 text-commatix-600 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-1">Workflow Automation</h4>
                                <p class="text-sm text-gray-600">Automate business processes, approvals, and multi-step workflows</p>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- Task Management -->
                <label class="cursor-pointer">
                    <input type="radio" name="use_case" value="task_management"
                           x-model="useCase" required
                           {{ old('use_case', $stepData['use_case'] ?? '') == 'task_management' ? 'checked' : '' }}
                           class="sr-only">
                    <div class="glass-input p-6 rounded-xl transition-all duration-200"
                         :class="useCase === 'task_management' ? 'ring-2 ring-commatix-500 bg-commatix-50' : 'hover:bg-white'">
                        <div class="flex items-start">
                            <svg class="w-8 h-8 text-commatix-600 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-1">Task Management</h4>
                                <p class="text-sm text-gray-600">Organize tasks, assign team members, and track project progress</p>
                            </div>
                        </div>
                    </div>
                </label>

                <!-- Multi-Channel Communication -->
                <label class="cursor-pointer">
                    <input type="radio" name="use_case" value="multi_channel"
                           x-model="useCase" required
                           {{ old('use_case', $stepData['use_case'] ?? '') == 'multi_channel' ? 'checked' : '' }}
                           class="sr-only">
                    <div class="glass-input p-6 rounded-xl transition-all duration-200"
                         :class="useCase === 'multi_channel' ? 'ring-2 ring-commatix-500 bg-commatix-50' : 'hover:bg-white'">
                        <div class="flex items-start">
                            <svg class="w-8 h-8 text-commatix-600 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                                <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                            </svg>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-1">Multi-Channel Communication</h4>
                                <p class="text-sm text-gray-600">Combine email, SMS, and workflows for comprehensive communication</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            @error('use_case')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Industry-Specific Workflow Templates -->
        <div class="mb-8" x-show="useCase === 'workflow_automation' || useCase === 'multi_channel'" x-transition>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Select Workflow Templates <span class="text-gray-400 text-sm font-normal">(optional)</span>
            </h3>

            <p class="text-sm text-gray-600 mb-4">We'll set up these industry-specific templates for you</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($workflowTemplates as $template)
                    <label class="cursor-pointer">
                        <div class="glass-input p-4 rounded-lg hover:bg-white transition-all duration-200 flex items-center">
                            <input type="checkbox"
                                   name="workflow_template_ids[]"
                                   value="{{ $template->id }}"
                                   {{ in_array($template->id, old('workflow_template_ids', $stepData['workflow_template_ids'] ?? [])) ? 'checked' : '' }}
                                   class="w-4 h-4 text-commatix-600 border-gray-300 rounded focus:ring-commatix-500">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $template->name }}</p>
                                @if($template->description)
                                    <p class="text-xs text-gray-500">{{ Str::limit($template->description, 50) }}</p>
                                @endif
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            @error('workflow_template_ids')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Use Case Benefits -->
        <div class="mb-8 p-6 bg-blue-50 rounded-xl border border-blue-200">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-blue-900 mb-1">What happens next?</h4>
                    <p class="text-sm text-blue-800" x-show="!useCase">Select a use case to see what we'll set up for you</p>

                    <div x-show="useCase === 'email_marketing'" x-cloak>
                        <p class="text-sm text-blue-800">We'll configure your email campaigns, create subscriber lists, and set up email templates for South African audiences.</p>
                    </div>

                    <div x-show="useCase === 'sms_campaigns'" x-cloak>
                        <p class="text-sm text-blue-800">We'll integrate SMS services (Vonage), set up sender IDs, and configure bulk messaging for South African mobile networks.</p>
                    </div>

                    <div x-show="useCase === 'workflow_automation'" x-cloak>
                        <p class="text-sm text-blue-800">We'll create automated workflows, set up approval chains, and configure milestone tracking for your industry.</p>
                    </div>

                    <div x-show="useCase === 'task_management'" x-cloak>
                        <p class="text-sm text-blue-800">We'll set up task boards, configure team assignments, and create project tracking dashboards.</p>
                    </div>

                    <div x-show="useCase === 'multi_channel'" x-cloak>
                        <p class="text-sm text-blue-800">We'll configure email, SMS, and workflows together for a complete communication solution.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons (Right-aligned per SA UX standards) -->
        <div class="flex items-center justify-between pt-8 mt-8 border-t-2 border-gray-100">
            <a href="{{ route('onboarding.step', 2) }}"
               class="btn-monday-secondary inline-flex items-center group">
                <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                Back
            </a>

            <button type="submit"
                    @click="action = 'next'"
                    class="btn-monday-primary inline-flex items-center group">
                Continue
                <svg class="w-5 h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function step3Form() {
    return {
        useCase: '{{ old('use_case', $stepData['use_case'] ?? '') }}',
        action: 'next',

        init() {
            // Initialize
        },

        handleSubmit(event) {
            // Form will submit normally
        }
    }
}
</script>
@endpush
