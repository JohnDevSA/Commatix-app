@extends('layouts.onboarding')

@section('title', 'SA Integrations')

@section('content')
<div x-data="step4Form()" x-init="init()">
    <div class="mb-10">
        <h2 class="text-4xl font-bold text-gray-900 mb-3">Connect your business tools</h2>
        <p class="text-lg text-gray-600">Integrate with popular South African business services (optional)</p>
    </div>

    <form method="POST" action="{{ route('onboarding.process', 4) }}" @submit="handleSubmit">
        @csrf
        <input type="hidden" name="action" x-model="action">

        <!-- Payment Gateway Integration -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                    </svg>
                    Payment Gateway
                </h3>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="configure_payment_now" value="1"
                           x-model="configurePayment"
                           {{ old('configure_payment_now', $stepData['configure_payment_now'] ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 text-commatix-600 border-gray-300 rounded focus:ring-commatix-500">
                    <span class="ml-2 text-sm text-gray-700">Configure payment gateway now</span>
                </label>
            </div>

            <div x-show="!configurePayment" class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-600">You can set up payment integrations later from your dashboard</p>
            </div>

            <div x-show="configurePayment" x-transition class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- PayFast -->
                    <div class="glass-input p-6 rounded-xl">
                        <div class="flex items-start mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">PayFast</h4>
                                <p class="text-xs text-gray-500">Most popular in SA</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Accept ZAR payments, subscriptions, and recurring billing</p>
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            We'll configure this for you
                        </div>
                    </div>

                    <!-- Yoco -->
                    <div class="glass-input p-6 rounded-xl">
                        <div class="flex items-start mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">Yoco</h4>
                                <p class="text-xs text-gray-500">Growing fast</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Modern payment processing for South African businesses</p>
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            We'll configure this for you
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accounting Software Integration -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                </svg>
                Accounting Software <span class="text-gray-400 text-sm font-normal">(optional)</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <!-- Sage -->
                <label class="cursor-pointer">
                    <input type="radio" name="accounting_software" value="sage"
                           x-model="accountingSoftware"
                           {{ old('accounting_software', $stepData['accounting_software'] ?? '') == 'sage' ? 'checked' : '' }}
                           class="sr-only">
                    <div class="glass-input p-6 rounded-xl text-center transition-all duration-200"
                         :class="accountingSoftware === 'sage' ? 'ring-2 ring-commatix-500 bg-commatix-50' : 'hover:bg-white'">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">Sage / Pastel</h4>
                        <p class="text-xs text-gray-500 mt-1">Business Cloud</p>
                    </div>
                </label>

                <!-- Xero -->
                <label class="cursor-pointer">
                    <input type="radio" name="accounting_software" value="xero"
                           x-model="accountingSoftware"
                           {{ old('accounting_software', $stepData['accounting_software'] ?? '') == 'xero' ? 'checked' : '' }}
                           class="sr-only">
                    <div class="glass-input p-6 rounded-xl text-center transition-all duration-200"
                         :class="accountingSoftware === 'xero' ? 'ring-2 ring-commatix-500 bg-commatix-50' : 'hover:bg-white'">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">Xero</h4>
                        <p class="text-xs text-gray-500 mt-1">Cloud Accounting</p>
                    </div>
                </label>

                <!-- Other -->
                <label class="cursor-pointer">
                    <input type="radio" name="accounting_software" value="other"
                           x-model="accountingSoftware"
                           {{ old('accounting_software', $stepData['accounting_software'] ?? '') == 'other' ? 'checked' : '' }}
                           class="sr-only">
                    <div class="glass-input p-6 rounded-xl text-center transition-all duration-200"
                         :class="accountingSoftware === 'other' ? 'ring-2 ring-commatix-500 bg-commatix-50' : 'hover:bg-white'">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900">Other</h4>
                        <p class="text-xs text-gray-500 mt-1">Specify below</p>
                    </div>
                </label>

                <!-- None -->
                <label class="cursor-pointer md:col-span-3">
                    <input type="radio" name="accounting_software" value="none"
                           x-model="accountingSoftware"
                           {{ old('accounting_software', $stepData['accounting_software'] ?? 'none') == 'none' ? 'checked' : '' }}
                           class="sr-only">
                    <div class="glass-input p-4 rounded-xl transition-all duration-200 flex items-center justify-center"
                         :class="accountingSoftware === 'none' ? 'ring-2 ring-commatix-500 bg-commatix-50' : 'hover:bg-white'">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-gray-600">I don't use accounting software yet</span>
                    </div>
                </label>
            </div>

            <div x-show="accountingSoftware === 'other'" x-transition class="mt-4">
                <label for="accounting_software_other" class="block text-sm font-medium text-gray-700 mb-2">
                    Which accounting software do you use?
                </label>
                <input type="text" id="accounting_software_other" name="accounting_software_other"
                       :required="accountingSoftware === 'other'"
                       value="{{ old('accounting_software_other', $stepData['accounting_software_other'] ?? '') }}"
                       class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                       placeholder="e.g., QuickBooks, FreshBooks, etc.">
                @error('accounting_software_other')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div x-show="accountingSoftware && accountingSoftware !== 'none' && accountingSoftware !== 'other'" x-transition class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-green-800">Great choice! We'll help you connect to <span x-text="accountingSoftware === 'sage' ? 'Sage Business Cloud' : 'Xero'"></span> after onboarding.</p>
                </div>
            </div>
        </div>

        <!-- Integration Benefits -->
        <div class="mb-8 p-6 glass-card rounded-xl border-l-4 border-commatix-500">
            <h4 class="text-sm font-semibold text-gray-900 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"/>
                </svg>
                Why connect integrations?
            </h4>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Automatic invoice syncing with your accounting software
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Accept payments directly through Commatix workflows
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Real-time financial reporting and reconciliation
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Don't worry - you can configure all of this later!
                </li>
            </ul>
        </div>

        <!-- Action Buttons (Right-aligned per SA UX standards) -->
        <div class="flex items-center justify-between pt-8 mt-8 border-t-2 border-gray-100">
            <a href="{{ route('onboarding.step', 3) }}"
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
function step4Form() {
    return {
        configurePayment: {{ old('configure_payment_now', $stepData['configure_payment_now'] ?? false) ? 'true' : 'false' }},
        accountingSoftware: '{{ old('accounting_software', $stepData['accounting_software'] ?? 'none') }}',
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
