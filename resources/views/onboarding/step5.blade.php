@extends('layouts.onboarding')

@section('title', 'POPIA Consent')

@section('content')
<div x-data="step5Form()" x-init="init()">
    <div class="mb-10">
        <h2 class="text-4xl font-bold text-gray-900 mb-3">Data Protection & Privacy</h2>
        <p class="text-lg text-gray-600">Your privacy matters - POPIA compliance is mandatory in South Africa</p>
    </div>

    <form method="POST" action="{{ route('onboarding.process', 5) }}" @submit="handleSubmit">
        @csrf
        <input type="hidden" name="action" x-model="action">

        <!-- POPIA Information -->
        <div class="mb-8 p-6 bg-blue-50 rounded-xl border-2 border-blue-200">
            <div class="flex items-start">
                <svg class="w-8 h-8 text-blue-600 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">What is POPIA?</h3>
                    <p class="text-sm text-blue-800 mb-3">
                        The Protection of Personal Information Act (POPIA) is South Africa's data protection law.
                        It gives you control over your personal information and ensures we handle your data responsibly.
                    </p>
                    <p class="text-sm text-blue-800">
                        We're required by law to explain how we'll use your data and get your explicit consent.
                    </p>
                </div>
            </div>
        </div>

        <!-- How We Use Your Data -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                How We'll Use Your Data
            </h3>

            <div class="space-y-4">
                <div class="glass-input p-5 rounded-xl">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Essential Service Delivery</h4>
                            <p class="text-sm text-gray-600">
                                We'll use your company and contact information to provide you with Commatix services,
                                manage your account, and ensure smooth operation of workflows and communications.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="glass-input p-5 rounded-xl">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Billing & Support</h4>
                            <p class="text-sm text-gray-600">
                                We'll process billing information, send invoices, and provide customer support when you need help.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="glass-input p-5 rounded-xl">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Legal Compliance</h4>
                            <p class="text-sm text-gray-600">
                                We'll keep records as required by South African law (Companies Act, Tax Administration Act, etc.)
                                and protect your data according to POPIA standards.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="glass-input p-5 rounded-xl">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Service Improvement</h4>
                            <p class="text-sm text-gray-600">
                                We'll analyze usage patterns (anonymized) to improve Commatix and develop new features
                                tailored to South African businesses.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Your Rights Under POPIA -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/>
                </svg>
                Your Rights
            </h3>

            <div class="glass-input p-6 rounded-xl">
                <ul class="space-y-3 text-sm text-gray-700">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-commatix-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span><strong>Access:</strong> You can request a copy of all personal information we hold about you</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-commatix-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span><strong>Correction:</strong> You can request corrections to inaccurate or outdated information</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-commatix-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span><strong>Deletion:</strong> You can request deletion of your data (subject to legal retention requirements)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-commatix-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span><strong>Object:</strong> You can object to certain processing of your personal information</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-commatix-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span><strong>Withdraw Consent:</strong> You can withdraw your consent at any time (for non-essential processing)</span>
                    </li>
                </ul>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs text-gray-600">
                        To exercise your rights, contact us at <a href="mailto:privacy@commatix.co.za" class="text-commatix-600 hover:underline">privacy@commatix.co.za</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Consent Checkboxes -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Your Consent <span class="text-red-500">*</span>
            </h3>

            <div class="space-y-4">
                <!-- Mandatory Processing Consent -->
                <div class="glass-input p-5 rounded-xl border-2" :class="popiaConsent ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" name="popia_consent" value="1" required
                               x-model="popiaConsent"
                               {{ old('popia_consent', $stepData['popia_consent'] ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 text-commatix-600 border-gray-300 rounded focus:ring-commatix-500 mt-0.5 flex-shrink-0">
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-gray-900">
                                I consent to Commatix processing my personal information
                            </p>
                            <p class="text-xs text-gray-600 mt-1">
                                I understand and agree to Commatix collecting, using, and storing my personal information as described above
                                to provide services, process billing, ensure legal compliance, and improve the platform. This consent is required to use Commatix.
                            </p>
                        </div>
                    </label>
                    @error('popia_consent')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Optional Marketing Consent -->
                <div class="glass-input p-5 rounded-xl" :class="marketingConsent ? 'border-2 border-commatix-500 bg-commatix-50' : ''">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" name="marketing_consent" value="1"
                               x-model="marketingConsent"
                               {{ old('marketing_consent', $stepData['marketing_consent'] ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 text-commatix-600 border-gray-300 rounded focus:ring-commatix-500 mt-0.5 flex-shrink-0">
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-gray-900">
                                I'd like to receive product updates and tips <span class="text-gray-400 font-normal">(optional)</span>
                            </p>
                            <p class="text-xs text-gray-600 mt-1">
                                Get the most out of Commatix with feature updates, best practices, and South African business tips.
                                You can unsubscribe anytime.
                            </p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Privacy Policy Link -->
        <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
            <p class="text-sm text-gray-600">
                For full details, read our
                <a href="#" target="_blank" class="text-commatix-600 hover:underline font-medium">Privacy Policy</a> and
                <a href="#" target="_blank" class="text-commatix-600 hover:underline font-medium">Terms of Service</a>
            </p>
        </div>

        <!-- Action Buttons (Right-aligned per SA UX standards) -->
        <div class="flex items-center justify-between pt-8 mt-8 border-t-2 border-gray-100">
            <a href="{{ route('onboarding.step', 4) }}"
               class="btn-monday-secondary inline-flex items-center group">
                <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                Back
            </a>

            <button type="submit"
                    @click="action = 'next'"
                    :disabled="!popiaConsent"
                    class="btn-monday-primary inline-flex items-center group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
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
function step5Form() {
    return {
        popiaConsent: {{ old('popia_consent', $stepData['popia_consent'] ?? false) ? 'true' : 'false' }},
        marketingConsent: {{ old('marketing_consent', $stepData['marketing_consent'] ?? false) ? 'true' : 'false' }},
        action: 'next',

        init() {
            // Initialize
        },

        handleSubmit(event) {
            if (!this.popiaConsent) {
                event.preventDefault();
                alert('You must consent to data processing to use Commatix. This is required by POPIA.');
            }
        }
    }
}
</script>
@endpush
