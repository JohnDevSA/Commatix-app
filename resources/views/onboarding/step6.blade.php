@extends('layouts.onboarding')

@section('title', 'Choose Your Plan')

@section('content')
<div x-data="step6Form()" x-init="init()">
    <div class="mb-10 text-center">
        <h2 class="text-4xl font-bold text-gray-900 mb-3">Choose the right plan for you</h2>
        <p class="text-lg text-gray-600">Start with a 14-day free trial. No credit card required.</p>
    </div>

    <form method="POST" action="{{ route('onboarding.process', 6) }}" @submit="handleSubmit">
        @csrf
        <input type="hidden" name="action" x-model="action">

        <!-- Billing Cycle Toggle -->
        <div class="flex items-center justify-center mb-8">
            <label class="flex items-center glass-input px-6 py-3 rounded-full cursor-pointer">
                <span class="text-sm font-medium mr-3" :class="billingCycle === 'monthly' ? 'text-commatix-600' : 'text-gray-600'">
                    Monthly
                </span>
                <input type="hidden" name="billing_cycle" x-model="billingCycle">
                <div class="relative inline-block w-14 mr-3 align-middle select-none">
                    <input type="checkbox"
                           x-model="isAnnual"
                           @change="billingCycle = isAnnual ? 'annual' : 'monthly'"
                           class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-transform duration-200 ease-in-out"
                           :class="isAnnual ? 'translate-x-8 border-commatix-500' : 'border-gray-300'">
                    <label class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-colors duration-200"
                           :class="isAnnual ? 'bg-commatix-500' : 'bg-gray-300'">
                    </label>
                </div>
                <span class="text-sm font-medium" :class="billingCycle === 'annual' ? 'text-commatix-600' : 'text-gray-600'">
                    Annual
                    <span class="ml-1 px-2 py-0.5 text-xs bg-sa-gold text-gray-900 rounded-full font-semibold">Save 20%</span>
                </span>
            </label>
        </div>

        <!-- Pricing Plans -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Starter Plan -->
            <label class="cursor-pointer">
                <input type="radio" name="selected_plan" value="starter" required
                       x-model="selectedPlan"
                       {{ old('selected_plan', $stepData['selected_plan'] ?? '') == 'starter' ? 'checked' : '' }}
                       class="sr-only">
                <div class="glass-card rounded-2xl p-8 h-full transition-all duration-300 hover:shadow-2xl"
                     :class="selectedPlan === 'starter' ? 'ring-2 ring-commatix-500 bg-commatix-50' : 'hover:bg-white'">

                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter</h3>
                        <p class="text-sm text-gray-600 mb-4">Perfect for small businesses getting started</p>
                        <div class="mb-2">
                            <span class="text-4xl font-bold text-gray-900">
                                R <span x-text="billingCycle === 'monthly' ? '499' : '399'"></span>
                            </span>
                            <span class="text-gray-600">/month</span>
                        </div>
                        <p class="text-xs text-gray-500">
                            <span x-show="billingCycle === 'annual'">R 4,788/year (billed annually)</span>
                            <span x-show="billingCycle === 'monthly'">Billed monthly</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Excluding VAT (15%)</p>
                    </div>

                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Up to 5 team members</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>500 emails/month</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>250 SMS/month</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>3 active workflows</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Basic support</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>2GB storage</span>
                        </li>
                    </ul>

                    <div x-show="selectedPlan === 'starter'" class="flex items-center justify-center text-commatix-600 font-semibold">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Selected
                    </div>
                </div>
            </label>

            <!-- Professional Plan (Most Popular) -->
            <label class="cursor-pointer relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 px-4 py-1 bg-gradient-to-r from-commatix-500 to-purple-500 text-white text-xs font-bold rounded-full shadow-lg">
                    MOST POPULAR
                </div>
                <input type="radio" name="selected_plan" value="professional" required
                       x-model="selectedPlan"
                       {{ old('selected_plan', $stepData['selected_plan'] ?? 'professional') == 'professional' ? 'checked' : '' }}
                       class="sr-only">
                <div class="glass-card rounded-2xl p-8 h-full transition-all duration-300 hover:shadow-2xl border-2"
                     :class="selectedPlan === 'professional' ? 'ring-2 ring-commatix-500 bg-commatix-50 border-commatix-500' : 'border-transparent hover:bg-white'">

                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Professional</h3>
                        <p class="text-sm text-gray-600 mb-4">Ideal for growing businesses</p>
                        <div class="mb-2">
                            <span class="text-4xl font-bold text-gray-900">
                                R <span x-text="billingCycle === 'monthly' ? '999' : '799'"></span>
                            </span>
                            <span class="text-gray-600">/month</span>
                        </div>
                        <p class="text-xs text-gray-500">
                            <span x-show="billingCycle === 'annual'">R 9,588/year (billed annually)</span>
                            <span x-show="billingCycle === 'monthly'">Billed monthly</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Excluding VAT (15%)</p>
                    </div>

                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Up to 25 team members</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>5,000 emails/month</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>2,500 SMS/month</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Unlimited workflows</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Priority support</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>25GB storage</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Advanced integrations</span>
                        </li>
                    </ul>

                    <div x-show="selectedPlan === 'professional'" class="flex items-center justify-center text-commatix-600 font-semibold">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Selected
                    </div>
                </div>
            </label>

            <!-- Enterprise Plan -->
            <label class="cursor-pointer">
                <input type="radio" name="selected_plan" value="enterprise" required
                       x-model="selectedPlan"
                       {{ old('selected_plan', $stepData['selected_plan'] ?? '') == 'enterprise' ? 'checked' : '' }}
                       class="sr-only">
                <div class="glass-card rounded-2xl p-8 h-full transition-all duration-300 hover:shadow-2xl"
                     :class="selectedPlan === 'enterprise' ? 'ring-2 ring-commatix-500 bg-commatix-50' : 'hover:bg-white'">

                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Enterprise</h3>
                        <p class="text-sm text-gray-600 mb-4">For large organizations with custom needs</p>
                        <div class="mb-2">
                            <span class="text-4xl font-bold text-gray-900">
                                R <span x-text="billingCycle === 'monthly' ? '2,499' : '1,999'"></span>
                            </span>
                            <span class="text-gray-600">/month</span>
                        </div>
                        <p class="text-xs text-gray-500">
                            <span x-show="billingCycle === 'annual'">R 23,988/year (billed annually)</span>
                            <span x-show="billingCycle === 'monthly'">Billed monthly</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Excluding VAT (15%)</p>
                    </div>

                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Unlimited team members</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>50,000 emails/month</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>25,000 SMS/month</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Unlimited workflows</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Dedicated support</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Unlimited storage</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Custom integrations</span>
                        </li>
                        <li class="flex items-start text-sm">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>SLA guarantee</span>
                        </li>
                    </ul>

                    <div x-show="selectedPlan === 'enterprise'" class="flex items-center justify-center text-commatix-600 font-semibold">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Selected
                    </div>
                </div>
            </label>
        </div>

        @error('selected_plan')
            <p class="text-center mb-4 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <!-- Plan Benefits -->
        <div class="mb-8 p-6 glass-card rounded-xl border-l-4 border-sa-gold">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">All plans include:</h4>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            14-day free trial
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            No credit card required
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Cancel anytime
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            ZAR pricing (no forex fees)
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            SA-based data hosting
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            POPIA compliant
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Annual Discount Info -->
        <div x-show="billingCycle === 'annual'" x-transition class="mb-8 p-4 bg-green-50 rounded-lg border border-green-200 text-center">
            <p class="text-sm text-green-800 font-medium">
                🎉 You're saving <span x-text="selectedPlan === 'starter' ? 'R 1,200' : (selectedPlan === 'professional' ? 'R 2,400' : 'R 6,000')"></span> per year with annual billing!
            </p>
        </div>

        <!-- Action Buttons (Right-aligned per SA UX standards) -->
        <div class="flex items-center justify-between pt-8 mt-8 border-t-2 border-gray-100">
            <a href="{{ route('onboarding.step', 5) }}"
               class="btn-monday-secondary inline-flex items-center group">
                <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                Back
            </a>

            <button type="submit"
                    @click="action = 'next'"
                    class="btn-monday-primary inline-flex items-center group text-lg">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Complete Setup
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function step6Form() {
    return {
        selectedPlan: '{{ old('selected_plan', $stepData['selected_plan'] ?? 'professional') }}',
        billingCycle: '{{ old('billing_cycle', $stepData['billing_cycle'] ?? 'monthly') }}',
        isAnnual: {{ old('billing_cycle', $stepData['billing_cycle'] ?? 'monthly') === 'annual' ? 'true' : 'false' }},
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

<style>
.toggle-checkbox:checked {
    right: 0;
}
</style>
@endpush
