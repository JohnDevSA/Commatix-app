@extends('layouts.onboarding-modern')

@section('title', 'Privacy & Compliance')

@section('content')
<div x-data="step5Modern()" x-init="init()" class="slide-enter">
    <form @submit.prevent="handleSubmit">
        @csrf

        <!-- Question Container -->
        <div class="question-card p-8 md:p-12 mb-8">
            <!-- Step Indicator -->
            <div class="text-sm font-semibold text-blue-600 mb-4">
                Step 5 of 6 • Almost done!
            </div>

            <!-- Question 1: POPIA Overview -->
            <template x-if="currentQuestion === 0">
                <div>
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>

                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 text-center">
                        Your privacy matters to us
                    </h2>
                    <p class="text-lg text-gray-600 mb-8 text-center max-w-xl mx-auto">
                        We're compliant with South Africa's POPIA (Protection of Personal Information Act)
                    </p>

                    <div class="space-y-4 mb-8">
                        <!-- What we collect -->
                        <div class="p-6 bg-blue-50 rounded-xl border border-blue-200">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-2">What we collect</h3>
                                    <p class="text-sm text-gray-700">Company details, contact information, and usage data to provide our services</p>
                                </div>
                            </div>
                        </div>

                        <!-- How we use it -->
                        <div class="p-6 bg-purple-50 rounded-xl border border-purple-200">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 rounded-lg bg-purple-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-2">How we use it</h3>
                                    <p class="text-sm text-gray-700">To deliver our services, improve your experience, and communicate important updates</p>
                                </div>
                            </div>
                        </div>

                        <!-- Your rights -->
                        <div class="p-6 bg-green-50 rounded-xl border border-green-200">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 rounded-lg bg-green-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Your rights</h3>
                                    <p class="text-sm text-gray-700">Access, update, or delete your data anytime. You're always in control</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button
                            type="button"
                            @click="nextQuestion"
                            class="btn-primary-modern inline-flex items-center">
                            I Understand
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>

            <!-- Question 2: Consent Checkboxes -->
            <template x-if="currentQuestion === 1">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        We need your consent
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Please review and accept the following
                    </p>

                    <div class="space-y-4">
                        <!-- Terms of Service -->
                        <label class="option-card cursor-pointer"
                               :class="{ 'selected': formData.accept_terms }">
                            <div class="flex items-start space-x-4">
                                <input
                                    type="checkbox"
                                    name="accept_terms"
                                    x-model="formData.accept_terms"
                                    required
                                    class="w-6 h-6 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 mt-1"
                                />
                                <div class="flex-1">
                                    <div class="text-lg font-medium text-gray-900 mb-1">
                                        I accept the Terms of Service <span class="text-red-500">*</span>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        By using Commatix, you agree to our
                                        <a href="/terms" target="_blank" class="text-blue-600 hover:underline">Terms of Service</a>
                                    </p>
                                </div>
                            </div>
                        </label>

                        <!-- Privacy Policy -->
                        <label class="option-card cursor-pointer"
                               :class="{ 'selected': formData.accept_privacy }">
                            <div class="flex items-start space-x-4">
                                <input
                                    type="checkbox"
                                    name="accept_privacy"
                                    x-model="formData.accept_privacy"
                                    required
                                    class="w-6 h-6 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 mt-1"
                                />
                                <div class="flex-1">
                                    <div class="text-lg font-medium text-gray-900 mb-1">
                                        I accept the Privacy Policy (POPIA Compliant) <span class="text-red-500">*</span>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        We'll handle your data according to our
                                        <a href="/privacy" target="_blank" class="text-blue-600 hover:underline">Privacy Policy</a>
                                    </p>
                                </div>
                            </div>
                        </label>

                        <!-- Marketing Consent (Optional) -->
                        <label class="option-card cursor-pointer"
                               :class="{ 'selected': formData.marketing_consent }">
                            <div class="flex items-start space-x-4">
                                <input
                                    type="checkbox"
                                    name="marketing_consent"
                                    x-model="formData.marketing_consent"
                                    class="w-6 h-6 text-purple-600 border-gray-300 rounded focus:ring-2 focus:ring-purple-500 mt-1"
                                />
                                <div class="flex-1">
                                    <div class="text-lg font-medium text-gray-900 mb-1">
                                        Send me product updates and tips <span class="text-gray-400 text-sm">(optional)</span>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        Get helpful tips, feature announcements, and special offers. You can unsubscribe anytime.
                                    </p>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="mt-8 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-600 leading-relaxed">
                            <strong>POPIA Compliance:</strong> Commatix is fully compliant with South Africa's Protection of Personal Information Act (POPIA).
                            Your data is encrypted, stored securely in South Africa, and never shared without your explicit consent.
                        </p>
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
                type="submit"
                x-show="currentQuestion === 1"
                :disabled="!canSubmit()"
                class="btn-primary-modern inline-flex items-center">
                Accept & Continue
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
function step5Modern() {
    return {
        currentQuestion: 0,
        formData: {
            accept_terms: {{ old('accept_terms', $stepData['accept_terms'] ?? 'false') }},
            accept_privacy: {{ old('accept_privacy', $stepData['accept_privacy'] ?? 'false') }},
            marketing_consent: {{ old('marketing_consent', $stepData['marketing_consent'] ?? 'false') }}
        },

        init() {
            document.addEventListener('keydown', (e) => {
                if (e.altKey && e.key === 'ArrowRight') {
                    e.preventDefault();
                    if (this.currentQuestion === 0) {
                        this.nextQuestion();
                    } else if (this.canSubmit()) {
                        this.$el.querySelector('form').submit();
                    }
                }
                if (e.altKey && e.key === 'ArrowLeft') {
                    e.preventDefault();
                    this.previousQuestion();
                }
            });
        },

        canSubmit() {
            return this.formData.accept_terms && this.formData.accept_privacy;
        },

        nextQuestion() {
            if (this.currentQuestion < 1) {
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
                alert('Please accept the Terms of Service and Privacy Policy to continue');
                return;
            }
            event.target.submit();
        }
    }
}
</script>
@endpush
