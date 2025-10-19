@extends('layouts.onboarding-modern')

@section('title', 'How You'll Use Commatix')

@section('content')
<div x-data="step3Modern()" x-init="init()" class="slide-enter">
    <form @submit.prevent="handleSubmit">
        @csrf

        <!-- Question Container -->
        <div class="question-card p-8 md:p-12 mb-8">
            <!-- Question Number -->
            <div class="text-sm font-semibold text-blue-600 mb-4">
                Question {{ currentQuestion + 1 }} of {{ questions.length }}
            </div>

            <!-- Question 1: Primary Use Case -->
            <template x-if="currentQuestion === 0">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        What's your main goal with Commatix?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        We'll customize your setup based on what matters most to you
                    </p>
                    <div class="space-y-4">
                        @foreach([
                            'email_marketing' => [
                                'label' => 'Email Marketing',
                                'description' => 'Build campaigns, newsletters, and subscriber lists',
                                'icon' => 'M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z',
                                'color' => 'blue'
                            ],
                            'sms_campaigns' => [
                                'label' => 'SMS Campaigns',
                                'description' => 'Send bulk SMS and mobile marketing messages',
                                'icon' => 'M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z',
                                'color' => 'green'
                            ],
                            'workflow_automation' => [
                                'label' => 'Workflow Automation',
                                'description' => 'Automate processes, approvals, and multi-step workflows',
                                'icon' => 'M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z',
                                'color' => 'purple'
                            ],
                            'task_management' => [
                                'label' => 'Task Management',
                                'description' => 'Organize tasks, assign teams, track project progress',
                                'icon' => 'M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z',
                                'color' => 'orange'
                            ],
                            'multi_channel' => [
                                'label' => 'All-in-One Communication',
                                'description' => 'Combine email, SMS, and workflows together',
                                'icon' => 'M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z',
                                'color' => 'pink'
                            ],
                        ] as $value => $data)
                            <label class="option-card"
                                   :class="{ 'selected': formData.use_case === '{{ $value }}' }">
                                <input
                                    type="radio"
                                    name="use_case"
                                    value="{{ $value }}"
                                    x-model="formData.use_case"
                                    class="sr-only"
                                    @change="handleUseCaseChange"
                                />
                                <div class="flex items-start space-x-4">
                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-{{ $data['color'] }}-500 to-{{ $data['color'] }}-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="{{ $data['icon'] }}"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xl font-semibold text-gray-900 mb-1">{{ $data['label'] }}</div>
                                        <div class="text-sm text-gray-600">{{ $data['description'] }}</div>
                                    </div>
                                    <svg x-show="formData.use_case === '{{ $value }}'"
                                         class="w-6 h-6 text-{{ $data['color'] }}-600 flex-shrink-0"
                                         fill="currentColor"
                                         viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- What happens next info -->
                    <div x-show="formData.use_case" x-transition class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <span x-show="formData.use_case === 'email_marketing'">We'll set up email campaigns, templates, and subscriber management</span>
                                <span x-show="formData.use_case === 'sms_campaigns'">We'll configure SMS services and bulk messaging for SA mobile networks</span>
                                <span x-show="formData.use_case === 'workflow_automation'">We'll create automated workflows and approval chains for your industry</span>
                                <span x-show="formData.use_case === 'task_management'">We'll set up task boards and project tracking dashboards</span>
                                <span x-show="formData.use_case === 'multi_channel'">We'll configure a complete communication solution across all channels</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Question 2: Workflow Templates (conditional) -->
            <template x-if="currentQuestion === 1 && needsWorkflowTemplates()">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Want pre-built workflow templates?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Select templates we'll set up for you (optional - you can skip this)
                    </p>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($workflowTemplates ?? [] as $template)
                            <label class="option-card cursor-pointer"
                                   :class="{ 'selected': selectedTemplates.includes({{ $template->id }}) }">
                                <input
                                    type="checkbox"
                                    name="workflow_template_ids[]"
                                    value="{{ $template->id }}"
                                    x-model="selectedTemplates"
                                    class="sr-only"
                                />
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-lg font-medium text-gray-900">{{ $template->name }}</div>
                                        @if($template->description)
                                            <div class="text-sm text-gray-600 mt-1">{{ Str::limit($template->description, 80) }}</div>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0 ml-4">
                                        <div x-show="selectedTemplates.includes({{ $template->id }})"
                                             class="w-6 h-6 bg-blue-600 rounded-md flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div x-show="!selectedTemplates.includes({{ $template->id }})"
                                             class="w-6 h-6 border-2 border-gray-300 rounded-md"></div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-4 text-sm text-gray-500">
                        Selected <span x-text="selectedTemplates.length"></span> template<span x-show="selectedTemplates.length !== 1">s</span>
                    </p>
                </div>
            </template>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex items-center justify-between">
            <button
                type="button"
                @click="previousQuestion"
                x-show="currentQuestion > 0"
                x-transition
                class="btn-secondary-modern inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>

            <div class="flex-1"></div>

            <button
                type="button"
                @click="nextQuestion"
                x-show="currentQuestion < questions.length - 1 && canProceed()"
                class="btn-primary-modern inline-flex items-center">
                <span x-show="currentQuestion === 1">Skip Templates</span>
                <span x-show="currentQuestion !== 1">Continue</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <button
                type="submit"
                x-show="currentQuestion === questions.length - 1 || !needsWorkflowTemplates()"
                :disabled="!canSubmit()"
                class="btn-primary-modern inline-flex items-center">
                Complete Step
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </div>

        <input type="hidden" name="action" value="next">
    </form>
</div>
@endsection

@push('scripts')
<script>
function step3Modern() {
    return {
        currentQuestion: 0,
        questions: [
            { field: 'use_case', required: true },
            { field: 'workflow_templates', required: false } // Conditional
        ],
        formData: {
            use_case: '{{ old("use_case", $stepData["use_case"] ?? "") }}'
        },
        selectedTemplates: @json(old('workflow_template_ids', $stepData['workflow_template_ids'] ?? [])),

        init() {
            document.addEventListener('keydown', (e) => {
                if (e.altKey && e.key === 'ArrowRight') {
                    e.preventDefault();
                    this.nextQuestion();
                }
                if (e.altKey && e.key === 'ArrowLeft') {
                    e.preventDefault();
                    this.previousQuestion();
                }
            });
        },

        needsWorkflowTemplates() {
            return this.formData.use_case === 'workflow_automation' ||
                   this.formData.use_case === 'multi_channel';
        },

        handleUseCaseChange() {
            // Auto-advance if use case doesn't need templates
            if (!this.needsWorkflowTemplates()) {
                setTimeout(() => {
                    // Submit directly
                    this.$el.querySelector('form').submit();
                }, 300);
            } else {
                // Move to templates question
                setTimeout(() => this.nextQuestion(), 300);
            }
        },

        canProceed() {
            if (this.currentQuestion === 0) {
                return this.formData.use_case !== '';
            }
            return true; // Templates are optional
        },

        canSubmit() {
            return this.formData.use_case !== '';
        },

        nextQuestion() {
            if (!this.canProceed()) return;

            if (this.currentQuestion < this.questions.length - 1) {
                this.currentQuestion++;
            }
        },

        previousQuestion() {
            if (this.currentQuestion > 0) {
                this.currentQuestion--;
            }
        },

        handleSubmit(event) {
            if (!this.canSubmit()) {
                alert('Please select your primary use case');
                return;
            }
            event.target.submit();
        }
    }
}
</script>
@endpush
