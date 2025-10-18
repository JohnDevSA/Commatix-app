@extends('layouts.onboarding')

@section('title', 'Company Information')

@section('content')
<div x-data="step1Form()" x-init="init()">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Tell us about your company</h2>
        <p class="text-gray-600">Let's start with some basic information about your business</p>
    </div>

    <form method="POST" action="{{ route('onboarding.process', 1) }}" @submit="handleSubmit">
        @csrf
        <input type="hidden" name="action" x-model="action">

        <!-- Basic Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                </svg>
                Basic Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Company Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="company_name" name="company_name" required
                           value="{{ old('company_name', $stepData['company_name'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="e.g., Acme (Pty) Ltd">
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="trading_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Trading Name <span class="text-gray-400 text-xs">(if different)</span>
                    </label>
                    <input type="text" id="trading_name" name="trading_name"
                           value="{{ old('trading_name', $stepData['trading_name'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="e.g., Acme Solutions">
                    @error('trading_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_registration_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Company Registration Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="company_registration_number" name="company_registration_number" required
                           value="{{ old('company_registration_number', $stepData['company_registration_number'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="2019/123456/07"
                           pattern="\d{4}/\d{6}/\d{2}">
                    <p class="mt-1 text-xs text-gray-500">Format: YYYY/NNNNNN/NN</p>
                    @error('company_registration_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="vat_number" class="block text-sm font-medium text-gray-700 mb-2">
                        VAT Number <span class="text-gray-400 text-xs">(optional)</span>
                    </label>
                    <input type="text" id="vat_number" name="vat_number"
                           value="{{ old('vat_number', $stepData['vat_number'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="4123456789"
                           maxlength="10">
                    <p class="mt-1 text-xs text-gray-500">10-digit VAT number</p>
                    @error('vat_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="tax_reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Tax Reference Number <span class="text-gray-400 text-xs">(optional)</span>
                    </label>
                    <input type="text" id="tax_reference_number" name="tax_reference_number"
                           value="{{ old('tax_reference_number', $stepData['tax_reference_number'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="9876543210">
                    <p class="mt-1 text-xs text-gray-500">SARS tax reference number</p>
                    @error('tax_reference_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Company Classification -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14 16a2 2 0 01-2-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2zM8 16a2 2 0 01-2-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H8z"/>
                </svg>
                Company Classification
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="industry_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Industry <span class="text-red-500">*</span>
                    </label>
                    <select id="industry_id" name="industry_id" required
                            class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500">
                        <option value="">Select an industry</option>
                        @foreach($industries as $industry)
                            <option value="{{ $industry->id }}"
                                    {{ old('industry_id', $stepData['industry_id'] ?? '') == $industry->id ? 'selected' : '' }}>
                                {{ $industry->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('industry_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Company Type <span class="text-red-500">*</span>
                    </label>
                    <select id="company_type" name="company_type" required
                            class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500">
                        <option value="">Select type</option>
                        @foreach([
                            'pty_ltd' => '(Pty) Ltd - Private Company',
                            'public' => 'Public Company',
                            'cc' => 'Close Corporation (CC)',
                            'partnership' => 'Partnership',
                            'sole_proprietor' => 'Sole Proprietor',
                            'npo' => 'Non-Profit Organization (NPO)',
                            'trust' => 'Trust'
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                    {{ old('company_type', $stepData['company_type'] ?? '') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bbee_level" class="block text-sm font-medium text-gray-700 mb-2">
                        B-BBEE Level <span class="text-gray-400 text-xs">(optional)</span>
                    </label>
                    <select id="bbee_level" name="bbee_level"
                            class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500">
                        <option value="">Select level</option>
                        @foreach([
                            'level_1' => 'Level 1 (135%)',
                            'level_2' => 'Level 2 (125%)',
                            'level_3' => 'Level 3 (110%)',
                            'level_4' => 'Level 4 (100%)',
                            'level_5' => 'Level 5 (80%)',
                            'level_6' => 'Level 6 (60%)',
                            'level_7' => 'Level 7 (50%)',
                            'level_8' => 'Level 8 (10%)',
                            'non_compliant' => 'Non-Compliant (0%)'
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                    {{ old('bbee_level', $stepData['bbee_level'] ?? '') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('bbee_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-3">
                    <label for="company_size" class="block text-sm font-medium text-gray-700 mb-2">
                        Company Size <span class="text-red-500">*</span>
                    </label>
                    <select id="company_size" name="company_size" required
                            class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500">
                        <option value="">Select size</option>
                        @foreach([
                            '1-5' => '1-5 employees',
                            '6-10' => '6-10 employees',
                            '11-25' => '11-25 employees',
                            '26-50' => '26-50 employees',
                            '50+' => '50+ employees'
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                    {{ old('company_size', $stepData['company_size'] ?? '') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_size')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                Contact Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="primary_contact_person" class="block text-sm font-medium text-gray-700 mb-2">
                        Primary Contact Person <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="primary_contact_person" name="primary_contact_person" required
                           value="{{ old('primary_contact_person', $stepData['primary_contact_person'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="John Smith">
                    @error('primary_contact_person')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="primary_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Primary Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="primary_email" name="primary_email" required
                           value="{{ old('primary_email', $stepData['primary_email'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="john@company.co.za">
                    @error('primary_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="primary_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Primary Phone <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" id="primary_phone" name="primary_phone" required
                           value="{{ old('primary_phone', $stepData['primary_phone'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="+27 12 345 6789">
                    <p class="mt-1 text-xs text-gray-500">Format: +27 XX XXX XXXX</p>
                    @error('primary_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Physical Address -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                Physical Address
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="physical_address_line1" class="block text-sm font-medium text-gray-700 mb-2">
                        Address Line 1 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="physical_address_line1" name="physical_address_line1" required
                           value="{{ old('physical_address_line1', $stepData['physical_address_line1'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="123 Main Street">
                    @error('physical_address_line1')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="physical_address_line2" class="block text-sm font-medium text-gray-700 mb-2">
                        Address Line 2 <span class="text-gray-400 text-xs">(optional)</span>
                    </label>
                    <input type="text" id="physical_address_line2" name="physical_address_line2"
                           value="{{ old('physical_address_line2', $stepData['physical_address_line2'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="Unit 4B">
                    @error('physical_address_line2')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="physical_city" class="block text-sm font-medium text-gray-700 mb-2">
                        City <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="physical_city" name="physical_city" required
                           value="{{ old('physical_city', $stepData['physical_city'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="Johannesburg">
                    @error('physical_city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="physical_province" class="block text-sm font-medium text-gray-700 mb-2">
                        Province <span class="text-red-500">*</span>
                    </label>
                    <select id="physical_province" name="physical_province" required
                            class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500">
                        <option value="">Select province</option>
                        @foreach($provinces as $code => $name)
                            <option value="{{ $code }}"
                                    {{ old('physical_province', $stepData['physical_province'] ?? '') == $code ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('physical_province')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="physical_postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Postal Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="physical_postal_code" name="physical_postal_code" required
                           value="{{ old('physical_postal_code', $stepData['physical_postal_code'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="2000"
                           pattern="\d{4}"
                           maxlength="4">
                    <p class="mt-1 text-xs text-gray-500">4-digit postal code</p>
                    @error('physical_postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Postal Address -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                    Postal Address
                </h3>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="same_as_physical" value="1"
                           x-model="sameAsPhysical"
                           {{ old('same_as_physical', $stepData['same_as_physical'] ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 text-commatix-600 border-gray-300 rounded focus:ring-commatix-500">
                    <span class="ml-2 text-sm text-gray-700">Same as physical address</span>
                </label>
            </div>

            <div x-show="!sameAsPhysical" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="postal_address_line1" class="block text-sm font-medium text-gray-700 mb-2">
                        Address Line 1 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="postal_address_line1" name="postal_address_line1"
                           :required="!sameAsPhysical"
                           value="{{ old('postal_address_line1', $stepData['postal_address_line1'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           placeholder="PO Box 12345">
                    @error('postal_address_line1')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="postal_address_line2" class="block text-sm font-medium text-gray-700 mb-2">
                        Address Line 2 <span class="text-gray-400 text-xs">(optional)</span>
                    </label>
                    <input type="text" id="postal_address_line2" name="postal_address_line2"
                           value="{{ old('postal_address_line2', $stepData['postal_address_line2'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500">
                    @error('postal_address_line2')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="postal_city" class="block text-sm font-medium text-gray-700 mb-2">
                        City <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="postal_city" name="postal_city"
                           :required="!sameAsPhysical"
                           value="{{ old('postal_city', $stepData['postal_city'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500">
                    @error('postal_city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="postal_province" class="block text-sm font-medium text-gray-700 mb-2">
                        Province <span class="text-red-500">*</span>
                    </label>
                    <select id="postal_province" name="postal_province"
                            :required="!sameAsPhysical"
                            class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500">
                        <option value="">Select province</option>
                        @foreach($provinces as $code => $name)
                            <option value="{{ $code }}"
                                    {{ old('postal_province', $stepData['postal_province'] ?? '') == $code ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('postal_province')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="postal_postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Postal Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="postal_postal_code" name="postal_postal_code"
                           :required="!sameAsPhysical"
                           value="{{ old('postal_postal_code', $stepData['postal_postal_code'] ?? '') }}"
                           class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500"
                           pattern="\d{4}"
                           maxlength="4">
                    @error('postal_postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons (Right-aligned per SA UX standards) -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <button type="submit"
                    @click="action = 'next'"
                    class="px-8 py-3 bg-commatix-500 text-white font-semibold rounded-lg hover:bg-opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center">
                Continue
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function step1Form() {
    return {
        sameAsPhysical: {{ old('same_as_physical', $stepData['same_as_physical'] ?? false) ? 'true' : 'false' }},
        action: 'next',

        init() {
            // Auto-save draft every 30 seconds
            setInterval(() => this.saveDraft(), 30000);
        },

        handleSubmit(event) {
            // Form will submit normally
        },

        saveDraft() {
            // Could implement auto-save here
            console.log('Auto-save draft');
        }
    }
}
</script>
@endpush
