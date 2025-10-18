@extends('layouts.onboarding')

@section('title', 'Your Role & Team')

@section('content')
<div x-data="step2Form()" x-init="init()">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Tell us about your role</h2>
        <p class="text-gray-600">Help us understand how you'll use Commatix</p>
    </div>

    <form method="POST" action="{{ route('onboarding.process', 2) }}" @submit="handleSubmit">
        @csrf
        <input type="hidden" name="action" x-model="action">

        <!-- Your Role -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                Your Role
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="user_role" class="block text-sm font-medium text-gray-700 mb-2">
                        What best describes your role? <span class="text-red-500">*</span>
                    </label>
                    <select id="user_role" name="user_role" required
                            class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500">
                        <option value="">Select your role</option>
                        @foreach([
                            'owner' => 'Business Owner',
                            'director' => 'Director',
                            'manager' => 'Manager',
                            'administrator' => 'Administrator',
                            'team_lead' => 'Team Lead',
                            'staff' => 'Staff Member',
                            'other' => 'Other'
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                    {{ old('user_role', $stepData['user_role'] ?? '') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="user_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                        User Type <span class="text-red-500">*</span>
                    </label>
                    <select id="user_type_id" name="user_type_id" required
                            class="glass-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-commatix-500">
                        <option value="">Select user type</option>
                        @foreach($userTypes as $userType)
                            <option value="{{ $userType->id }}"
                                    {{ old('user_type_id', $stepData['user_type_id'] ?? '') == $userType->id ? 'selected' : '' }}>
                                {{ $userType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Divisions/Departments -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                        <path d="M14 16a2 2 0 01-2-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2z"/>
                        <path d="M8 16a2 2 0 01-2-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H8z"/>
                    </svg>
                    Divisions/Departments
                </h3>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="has_divisions" value="1"
                           x-model="hasDivisions"
                           {{ old('has_divisions', $stepData['has_divisions'] ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 text-commatix-600 border-gray-300 rounded focus:ring-commatix-500">
                    <span class="ml-2 text-sm text-gray-700">My company has divisions/departments</span>
                </label>
            </div>

            <div x-show="hasDivisions" x-transition class="space-y-4">
                <template x-for="(division, index) in divisions" :key="index">
                    <div class="glass-input p-4 rounded-lg flex items-center space-x-4">
                        <div class="flex-1">
                            <input type="text"
                                   :name="'divisions[' + index + '][name]'"
                                   x-model="division.name"
                                   :required="hasDivisions"
                                   class="w-full px-4 py-2 border-0 bg-transparent focus:outline-none focus:ring-0"
                                   placeholder="e.g., Sales, Operations, Finance">
                        </div>
                        <button type="button"
                                @click="removeDivision(index)"
                                x-show="divisions.length > 1"
                                class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </template>

                <button type="button"
                        @click="addDivision"
                        x-show="divisions.length < 10"
                        class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-commatix-500 hover:text-commatix-600 transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Division
                </button>

                <p class="text-xs text-gray-500">Add your company's departments or divisions (max 10)</p>
            </div>
        </div>

        <!-- Team Invites -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-commatix-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                    </svg>
                    Invite Team Members
                </h3>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="invite_team_now" value="1"
                           x-model="inviteTeamNow"
                           {{ old('invite_team_now', $stepData['invite_team_now'] ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 text-commatix-600 border-gray-300 rounded focus:ring-commatix-500">
                    <span class="ml-2 text-sm text-gray-700">Invite team members now</span>
                </label>
            </div>

            <div x-show="inviteTeamNow" x-transition class="space-y-4">
                <template x-for="(invite, index) in teamInvites" :key="index">
                    <div class="glass-input p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                            <div class="md:col-span-2">
                                <input type="email"
                                       :name="'team_invites[' + index + '][email]'"
                                       x-model="invite.email"
                                       :required="inviteTeamNow"
                                       class="w-full px-4 py-2 border-0 bg-transparent focus:outline-none focus:ring-0"
                                       placeholder="colleague@company.co.za">
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="text"
                                       :name="'team_invites[' + index + '][name]'"
                                       x-model="invite.name"
                                       class="w-full px-4 py-2 border-0 bg-transparent focus:outline-none focus:ring-0"
                                       placeholder="Name (optional)">
                                <button type="button"
                                        @click="removeInvite(index)"
                                        x-show="teamInvites.length > 1"
                                        class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <button type="button"
                        @click="addInvite"
                        x-show="teamInvites.length < 20"
                        class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-commatix-500 hover:text-commatix-600 transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Team Member
                </button>

                <p class="text-xs text-gray-500">Enter email addresses of team members to invite (max 20)</p>
            </div>
        </div>

        <!-- Action Buttons (Right-aligned per SA UX standards) -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{ route('onboarding.step', 1) }}"
               class="px-6 py-3 text-gray-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                Back
            </a>

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
function step2Form() {
    return {
        hasDivisions: {{ old('has_divisions', $stepData['has_divisions'] ?? false) ? 'true' : 'false' }},
        inviteTeamNow: {{ old('invite_team_now', $stepData['invite_team_now'] ?? false) ? 'true' : 'false' }},
        divisions: @json(old('divisions', $stepData['divisions'] ?? [['name' => '']])),
        teamInvites: @json(old('team_invites', $stepData['team_invites'] ?? [['email' => '', 'name' => '']])),
        action: 'next',

        init() {
            if (this.divisions.length === 0) {
                this.divisions = [{ name: '' }];
            }
            if (this.teamInvites.length === 0) {
                this.teamInvites = [{ email: '', name: '' }];
            }
        },

        addDivision() {
            if (this.divisions.length < 10) {
                this.divisions.push({ name: '' });
            }
        },

        removeDivision(index) {
            if (this.divisions.length > 1) {
                this.divisions.splice(index, 1);
            }
        },

        addInvite() {
            if (this.teamInvites.length < 20) {
                this.teamInvites.push({ email: '', name: '' });
            }
        },

        removeInvite(index) {
            if (this.teamInvites.length > 1) {
                this.teamInvites.splice(index, 1);
            }
        },

        handleSubmit(event) {
            // Remove empty divisions and invites before submitting
            if (!this.hasDivisions) {
                this.divisions = [];
            }
            if (!this.inviteTeamNow) {
                this.teamInvites = [];
            }
        }
    }
}
</script>
@endpush
