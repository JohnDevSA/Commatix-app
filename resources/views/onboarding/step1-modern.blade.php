@extends('layouts.onboarding-modern')

@section('title', 'Company Information')

@section('content')
<div x-data="modernOnboarding()" x-init="init()" class="slide-enter">
    <form @submit.prevent="handleSubmit">
        @csrf

        <!-- Question Container -->
        <div class="question-card p-8 md:p-12 mb-8">
            <!-- Question Number -->
            <div class="text-sm font-semibold text-blue-600 mb-4">
                Question <span x-text="currentQuestion + 1"></span> of <span x-text="questions.length"></span>
            </div>

            <!-- Dynamic Question Content -->
            <template x-if="currentQuestion === 0">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        What's your company name?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        This is your legal/registered business name
                    </p>
                    <input
                        type="text"
                        name="company_name"
                        x-model="formData.company_name"
                        x-ref="question0"
                        @keydown.enter="nextQuestion"
                        required
                        class="modern-input"
                        placeholder="e.g., Acme Solutions (Pty) Ltd"
                        autofocus
                    />
                    @error('company_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-3 text-sm text-gray-500">
                        Press <kbd class="px-2 py-1 bg-gray-100 rounded">Enter</kbd> ↵ to continue
                    </p>
                </div>
            </template>

            <template x-if="currentQuestion === 1">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Company registration number?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Your official CIPC registration number
                    </p>
                    <input
                        type="text"
                        name="company_registration_number"
                        x-model="formData.company_registration_number"
                        x-ref="question1"
                        @keydown.enter="nextQuestion"
                        required
                        class="modern-input"
                        placeholder="2019/123456/07"
                        pattern="\d{4}/\d{6}/\d{2}"
                    />
                    <p class="mt-3 text-sm text-gray-500">
                        Format: YYYY/NNNNNN/NN
                    </p>
                </div>
            </template>

            <template x-if="currentQuestion === 2">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Which industry are you in?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        This helps us customize your experience
                    </p>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($industries as $industry)
                            <label class="option-card block"
                                   :class="{ 'selected': formData.industry_id === {{ $industry->id }} }">
                                <input
                                    type="radio"
                                    name="industry_id"
                                    value="{{ $industry->id }}"
                                    x-model="formData.industry_id"
                                    class="sr-only"
                                    @change="setTimeout(() => nextQuestion(), 300)"
                                />
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-medium text-gray-900">{{ $industry->name }}</span>
                                    <svg x-show="formData.industry_id === {{ $industry->id }}"
                                         class="w-6 h-6 text-blue-600"
                                         fill="currentColor"
                                         viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </template>

            <template x-if="currentQuestion === 3">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        How big is your team?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Approximate number of employees
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach([
                            '1-5' => '1-5 employees',
                            '6-10' => '6-10 employees',
                            '11-25' => '11-25 employees',
                            '26-50' => '26-50 employees',
                            '50+' => '50+ employees'
                        ] as $value => $label)
                            <label class="option-card"
                                   :class="{ 'selected': formData.company_size === '{{ $value }}' }">
                                <input
                                    type="radio"
                                    name="company_size"
                                    value="{{ $value }}"
                                    x-model="formData.company_size"
                                    class="sr-only"
                                    @change="setTimeout(() => nextQuestion(), 300)"
                                />
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-lg font-medium text-gray-900">{{ $label }}</div>
                                    </div>
                                    <svg x-show="formData.company_size === '{{ $value }}'"
                                         class="w-6 h-6 text-blue-600"
                                         fill="currentColor"
                                         viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </template>

            <template x-if="currentQuestion === 4">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        What's your contact email?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        We'll use this for important updates
                    </p>
                    <input
                        type="email"
                        name="primary_email"
                        x-model="formData.primary_email"
                        x-ref="question4"
                        @keydown.enter="nextQuestion"
                        required
                        class="modern-input"
                        placeholder="you@company.co.za"
                    />
                    <p class="mt-3 text-sm text-gray-500">
                        Press <kbd class="px-2 py-1 bg-gray-100 rounded">Enter</kbd> ↵
                    </p>
                </div>
            </template>

            <template x-if="currentQuestion === 5">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Contact phone number?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        For urgent communications
                    </p>
                    <input
                        type="tel"
                        name="primary_phone"
                        x-model="formData.primary_phone"
                        x-ref="question5"
                        @keydown.enter="nextQuestion"
                        required
                        class="modern-input"
                        placeholder="+27 12 345 6789"
                    />
                    <p class="mt-3 text-sm text-gray-500">
                        Format: +27 XX XXX XXXX
                    </p>
                </div>
            </template>

            <template x-if="currentQuestion === 6">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Where is your business located?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Your physical business address
                    </p>
                    <div class="space-y-4">
                        <input
                            type="text"
                            name="physical_address_line1"
                            x-model="formData.physical_address_line1"
                            required
                            class="modern-input"
                            placeholder="Street address"
                        />
                        <div class="grid grid-cols-2 gap-4">
                            <input
                                type="text"
                                name="physical_city"
                                x-model="formData.physical_city"
                                required
                                class="modern-input"
                                placeholder="City"
                            />
                            <input
                                type="text"
                                name="physical_postal_code"
                                x-model="formData.physical_postal_code"
                                required
                                pattern="\d{4}"
                                maxlength="4"
                                class="modern-input"
                                placeholder="Postal code"
                            />
                        </div>
                        <select
                            name="physical_province"
                            x-model="formData.physical_province"
                            required
                            class="modern-input">
                            <option value="">Select province</option>
                            @foreach($provinces as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </template>

            <template x-if="currentQuestion === 7">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Almost done! 🎉
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Let's wrap up with a few optional details
                    </p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Trading name (if different)
                            </label>
                            <input
                                type="text"
                                name="trading_name"
                                x-model="formData.trading_name"
                                class="modern-input"
                                placeholder="e.g., Acme Solutions"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                VAT Number (optional)
                            </label>
                            <input
                                type="text"
                                name="vat_number"
                                x-model="formData.vat_number"
                                maxlength="10"
                                class="modern-input"
                                placeholder="4123456789"
                            />
                        </div>
                    </div>
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
                Continue
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <button
                type="submit"
                x-show="currentQuestion === questions.length - 1"
                :disabled="!canSubmit()"
                class="btn-primary-modern inline-flex items-center">
                Complete Step
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </div>

        <!-- Hidden inputs for form submission -->
        <input type="hidden" name="action" value="next">
    </form>
</div>
@endsection

@push('scripts')
<script>
function modernOnboarding() {
    return {
        currentQuestion: 0,
        questions: [
            { field: 'company_name', required: true },
            { field: 'company_registration_number', required: true },
            { field: 'industry_id', required: true },
            { field: 'company_size', required: true },
            { field: 'primary_email', required: true },
            { field: 'primary_phone', required: true },
            { field: 'physical_address_line1', required: true },
            { field: 'trading_name', required: false }
        ],
        formData: {
            company_name: '{{ old("company_name", $stepData["company_name"] ?? "") }}',
            company_registration_number: '{{ old("company_registration_number", $stepData["company_registration_number"] ?? "") }}',
            industry_id: {{ old('industry_id', $stepData['industry_id'] ?? 'null') }},
            company_size: '{{ old("company_size", $stepData["company_size"] ?? "") }}',
            primary_email: '{{ old("primary_email", $stepData["primary_email"] ?? "") }}',
            primary_phone: '{{ old("primary_phone", $stepData["primary_phone"] ?? "") }}',
            physical_address_line1: '{{ old("physical_address_line1", $stepData["physical_address_line1"] ?? "") }}',
            physical_city: '{{ old("physical_city", $stepData["physical_city"] ?? "") }}',
            physical_postal_code: '{{ old("physical_postal_code", $stepData["physical_postal_code"] ?? "") }}',
            physical_province: '{{ old("physical_province", $stepData["physical_province"] ?? "") }}',
            trading_name: '{{ old("trading_name", $stepData["trading_name"] ?? "") }}',
            vat_number: '{{ old("vat_number", $stepData["vat_number"] ?? "") }}'
        },

        init() {
            // Auto-focus current question input
            this.$nextTick(() => {
                const ref = this.$refs['question' + this.currentQuestion];
                if (ref) {
                    ref.focus();
                }
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                // Alt + Right Arrow = Next
                if (e.altKey && e.key === 'ArrowRight') {
                    e.preventDefault();
                    this.nextQuestion();
                }
                // Alt + Left Arrow = Previous
                if (e.altKey && e.key === 'ArrowLeft') {
                    e.preventDefault();
                    this.previousQuestion();
                }
            });
        },

        canProceed() {
            const question = this.questions[this.currentQuestion];
            if (!question.required) return true;

            const value = this.formData[question.field];
            return value && value !== '' && value !== null;
        },

        canSubmit() {
            // Check all required fields are filled
            return this.questions.every(q => {
                if (!q.required) return true;
                const value = this.formData[q.field];
                return value && value !== '' && value !== null;
            });
        },

        nextQuestion() {
            if (!this.canProceed()) return;

            if (this.currentQuestion < this.questions.length - 1) {
                this.currentQuestion++;

                // Auto-focus next input
                this.$nextTick(() => {
                    const ref = this.$refs['question' + this.currentQuestion];
                    if (ref) {
                        ref.focus();
                    }
                });

                // Auto-save progress
                this.saveProgress();
            }
        },

        previousQuestion() {
            if (this.currentQuestion > 0) {
                this.currentQuestion--;

                // Auto-focus previous input
                this.$nextTick(() => {
                    const ref = this.$refs['question' + this.currentQuestion];
                    if (ref) {
                        ref.focus();
                    }
                });
            }
        },

        saveProgress() {
            // Could implement auto-save to session/localStorage
            console.log('Progress saved:', this.formData);
        },

        handleSubmit(event) {
            if (!this.canSubmit()) {
                alert('Please fill in all required fields');
                return;
            }
            event.target.submit();
        }
    }
}
</script>
@endpush
