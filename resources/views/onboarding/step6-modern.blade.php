@extends('layouts.onboarding-modern')

@section('title', 'Choose Your Plan')

@section('content')
<div x-data="step6Modern()" x-init="init()" class="slide-enter">
    <form @submit.prevent="handleSubmit">
        @csrf

        <!-- Question Container -->
        <div class="question-card p-8 md:p-12 mb-8">
            <!-- Step Indicator -->
            <div class="text-sm font-semibold text-blue-600 mb-4">
                Final Step • Choose your plan
            </div>

            <div class="text-center mb-8">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Choose the perfect plan for your business
                </h2>
                <p class="text-lg text-gray-600">
                    Start with our 14-day free trial. No credit card required.
                </p>
            </div>

            <!-- Billing Toggle -->
            <div class="flex items-center justify-center mb-8 space-x-4">
                <span class="text-sm font-medium" :class="billingCycle === 'monthly' ? 'text-gray-900' : 'text-gray-500'">
                    Monthly
                </span>
                <button
                    type="button"
                    @click="billingCycle = billingCycle === 'monthly' ? 'annual' : 'monthly'"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    :class="billingCycle === 'annual' ? 'bg-blue-600' : 'bg-gray-200'"
                    role="switch"
                    :aria-checked="billingCycle === 'annual'">
                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                          :class="billingCycle === 'annual' ? 'translate-x-5' : 'translate-x-0'">
                    </span>
                </button>
                <span class="text-sm font-medium" :class="billingCycle === 'annual' ? 'text-gray-900' : 'text-gray-500'">
                    Annual
                    <span class="ml-1 inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">
                        Save 20%
                    </span>
                </span>
            </div>

            <!-- Pricing Plans -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Starter Plan -->
                <label class="relative cursor-pointer">
                    <input
                        type="radio"
                        name="plan"
                        value="starter"
                        x-model="selectedPlan"
                        class="sr-only"
                    />
                    <div class="h-full p-6 rounded-2xl border-2 transition-all duration-200"
                         :class="selectedPlan === 'starter' ? 'border-blue-600 bg-blue-50 shadow-xl' : 'border-gray-200 bg-white hover:border-gray-300'">

                        <!-- Badge (if selected) -->
                        <div x-show="selectedPlan === 'starter'" class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="inline-flex items-center rounded-full bg-blue-600 px-4 py-1 text-xs font-semibold text-white">
                                Selected
                            </span>
                        </div>

                        <div class="text-center mb-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Starter</h3>
                            <div class="mb-2">
                                <span class="text-4xl font-bold text-gray-900">
                                    R<span x-text="billingCycle === 'monthly' ? '499' : '399'"></span>
                                </span>
                                <span class="text-gray-600">/month</span>
                            </div>
                            <p class="text-sm text-gray-600">Perfect for small businesses</p>
                        </div>

                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Up to 5 users</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>1,000 emails/month</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>500 SMS/month</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Basic workflow templates</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Email support</span>
                            </li>
                        </ul>
                    </div>
                </label>

                <!-- Professional Plan (Most Popular) -->
                <label class="relative cursor-pointer">
                    <input
                        type="radio"
                        name="plan"
                        value="professional"
                        x-model="selectedPlan"
                        class="sr-only"
                    />
                    <div class="h-full p-6 rounded-2xl border-2 transition-all duration-200 relative"
                         :class="selectedPlan === 'professional' ? 'border-purple-600 bg-purple-50 shadow-xl scale-105' : 'border-gray-200 bg-white hover:border-gray-300'">

                        <!-- Most Popular Badge -->
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="inline-flex items-center rounded-full px-4 py-1 text-xs font-semibold"
                                  :class="selectedPlan === 'professional' ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-800'">
                                Most Popular
                            </span>
                        </div>

                        <div class="text-center mb-4 mt-2">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Professional</h3>
                            <div class="mb-2">
                                <span class="text-4xl font-bold text-gray-900">
                                    R<span x-text="billingCycle === 'monthly' ? '999' : '799'"></span>
                                </span>
                                <span class="text-gray-600">/month</span>
                            </div>
                            <p class="text-sm text-gray-600">For growing businesses</p>
                        </div>

                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Up to 25 users</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>10,000 emails/month</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>5,000 SMS/month</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Advanced workflow automation</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Priority support</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>API access</span>
                            </li>
                        </ul>
                    </div>
                </label>

                <!-- Enterprise Plan -->
                <label class="relative cursor-pointer">
                    <input
                        type="radio"
                        name="plan"
                        value="enterprise"
                        x-model="selectedPlan"
                        class="sr-only"
                    />
                    <div class="h-full p-6 rounded-2xl border-2 transition-all duration-200"
                         :class="selectedPlan === 'enterprise' ? 'border-orange-600 bg-orange-50 shadow-xl' : 'border-gray-200 bg-white hover:border-gray-300'">

                        <div x-show="selectedPlan === 'enterprise'" class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="inline-flex items-center rounded-full bg-orange-600 px-4 py-1 text-xs font-semibold text-white">
                                Selected
                            </span>
                        </div>

                        <div class="text-center mb-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Enterprise</h3>
                            <div class="mb-2">
                                <span class="text-3xl font-bold text-gray-900">Custom</span>
                            </div>
                            <p class="text-sm text-gray-600">For large organizations</p>
                        </div>

                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Unlimited users</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Unlimited emails</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Custom SMS volume</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Custom integrations</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Dedicated account manager</span>
                            </li>
                            <li class="flex items-start text-sm">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>24/7 phone support</span>
                            </li>
                        </ul>
                    </div>
                </label>
            </div>

            <!-- Free Trial Info -->
            <div class="p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border-2 border-blue-200">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">14-day free trial included</h3>
                        <p class="text-sm text-gray-700">
                            Try any plan risk-free for 14 days. No credit card required. Cancel anytime during the trial with no charge.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('onboarding.step', 5) }}"
               class="btn-secondary-modern inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>

            <div class="flex-1"></div>

            <button
                type="submit"
                :disabled="!selectedPlan"
                class="btn-primary-modern inline-flex items-center text-lg px-8 py-4">
                Start Free Trial
                <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
        </div>

        <input type="hidden" name="action" value="complete">
        <input type="hidden" name="billing_cycle" x-model="billingCycle">
    </form>
</div>
@endsection

@push('scripts')
<script>
function step6Modern() {
    return {
        selectedPlan: '{{ old("plan", $stepData["plan"] ?? "professional") }}',
        billingCycle: '{{ old("billing_cycle", $stepData["billing_cycle"] ?? "monthly") }}',

        init() {
            document.addEventListener('keydown', (e) => {
                if (e.altKey && e.key === 'ArrowRight' && this.selectedPlan) {
                    e.preventDefault();
                    this.$el.querySelector('form').submit();
                }
            });
        },

        handleSubmit(event) {
            if (!this.selectedPlan) {
                alert('Please select a plan to continue');
                return;
            }
            event.target.submit();
        }
    }
}
</script>
@endpush
