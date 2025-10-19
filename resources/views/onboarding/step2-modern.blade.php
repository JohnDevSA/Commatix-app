@extends('layouts.onboarding-modern')

@section('title', 'Your Team')

@section('content')
<div x-data="step2Modern()" x-init="init()" class="slide-enter">
    <form @submit.prevent="handleSubmit">
        @csrf

        <!-- Question Container -->
        <div class="question-card p-8 md:p-12 mb-8">
            <!-- Question Number -->
            <div class="text-sm font-semibold text-blue-600 mb-4">
                Question {{ currentQuestion + 1 }} of {{ questions.length }}
            </div>

            <!-- Question 1: User Role -->
            <template x-if="currentQuestion === 0">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        What's your role in the company?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        This helps us understand how you'll use Commatix
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach([
                            'owner' => ['label' => 'Business Owner', 'icon' => 'M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z'],
                            'director' => ['label' => 'Director', 'icon' => 'M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z'],
                            'manager' => ['label' => 'Manager', 'icon' => 'M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z'],
                            'administrator' => ['label' => 'Administrator', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                            'team_lead' => ['label' => 'Team Lead', 'icon' => 'M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07z'],
                            'staff' => ['label' => 'Staff Member', 'icon' => 'M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z'],
                        ] as $value => $data)
                            <label class="option-card"
                                   :class="{ 'selected': formData.user_role === '{{ $value }}' }">
                                <input
                                    type="radio"
                                    name="user_role"
                                    value="{{ $value }}"
                                    x-model="formData.user_role"
                                    class="sr-only"
                                    @change="setTimeout(() => nextQuestion(), 300)"
                                />
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="{{ $data['icon'] }}"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-lg font-medium text-gray-900">{{ $data['label'] }}</div>
                                    </div>
                                    <svg x-show="formData.user_role === '{{ $value }}'"
                                         class="w-6 h-6 text-blue-600 flex-shrink-0"
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

            <!-- Question 2: User Type -->
            <template x-if="currentQuestion === 1">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Select your user type
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        This determines your access level and permissions
                    </p>
                    <div class="space-y-3">
                        @foreach($userTypes as $userType)
                            <label class="option-card"
                                   :class="{ 'selected': formData.user_type_id === {{ $userType->id }} }">
                                <input
                                    type="radio"
                                    name="user_type_id"
                                    value="{{ $userType->id }}"
                                    x-model="formData.user_type_id"
                                    class="sr-only"
                                    @change="setTimeout(() => nextQuestion(), 300)"
                                />
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-lg font-medium text-gray-900">{{ $userType->name }}</div>
                                        @if($userType->description)
                                            <div class="text-sm text-gray-600 mt-1">{{ $userType->description }}</div>
                                        @endif
                                    </div>
                                    <svg x-show="formData.user_type_id === {{ $userType->id }}"
                                         class="w-6 h-6 text-blue-600 flex-shrink-0"
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

            <!-- Question 3: Divisions -->
            <template x-if="currentQuestion === 2">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Does your company have divisions or departments?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        Organizing your team helps with task assignment and workflows
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <label class="option-card"
                               :class="{ 'selected': formData.has_divisions === true }">
                            <input
                                type="radio"
                                name="has_divisions"
                                :value="true"
                                x-model="formData.has_divisions"
                                class="sr-only"
                            />
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14 16a2 2 0 01-2-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2zM8 16a2 2 0 01-2-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H8z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-lg font-medium text-gray-900">Yes, we have divisions</div>
                                    <div class="text-sm text-gray-600">Set them up now</div>
                                </div>
                                <svg x-show="formData.has_divisions === true"
                                     class="w-6 h-6 text-green-600 flex-shrink-0"
                                     fill="currentColor"
                                     viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </label>

                        <label class="option-card"
                               :class="{ 'selected': formData.has_divisions === false }">
                            <input
                                type="radio"
                                name="has_divisions"
                                :value="false"
                                x-model="formData.has_divisions"
                                class="sr-only"
                                @change="setTimeout(() => nextQuestion(), 300)"
                            />
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-lg font-medium text-gray-900">No divisions</div>
                                    <div class="text-sm text-gray-600">Skip this step</div>
                                </div>
                                <svg x-show="formData.has_divisions === false"
                                     class="w-6 h-6 text-blue-600 flex-shrink-0"
                                     fill="currentColor"
                                     viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </label>
                    </div>

                    <!-- Division Input (conditional) -->
                    <div x-show="formData.has_divisions === true" x-transition class="space-y-4">
                        <div class="bg-blue-50 rounded-xl p-6 border-2 border-blue-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Add your divisions</h3>

                            <div class="space-y-3">
                                <template x-for="(division, index) in divisions" :key="index">
                                    <div class="flex items-center space-x-3">
                                        <input
                                            type="text"
                                            :name="'divisions[' + index + '][name]'"
                                            x-model="division.name"
                                            class="modern-input flex-1"
                                            placeholder="e.g., Sales, Operations, Finance"
                                        />
                                        <button
                                            type="button"
                                            @click="removeDivision(index)"
                                            x-show="divisions.length > 1"
                                            class="p-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <button
                                type="button"
                                @click="addDivision"
                                x-show="divisions.length < 10"
                                class="mt-4 w-full px-4 py-3 border-2 border-dashed border-blue-300 rounded-lg text-blue-600 hover:bg-blue-100 transition-colors font-medium">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Another Division
                            </button>
                            <p class="mt-3 text-sm text-gray-600">You can add up to 10 divisions</p>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Question 4: Team Invites -->
            <template x-if="currentQuestion === 3">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Want to invite your team now?
                    </h2>
                    <p class="text-lg text-gray-600 mb-8">
                        You can always invite them later from your dashboard
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <label class="option-card"
                               :class="{ 'selected': formData.invite_team_now === true }">
                            <input
                                type="radio"
                                name="invite_team_now"
                                :value="true"
                                x-model="formData.invite_team_now"
                                class="sr-only"
                            />
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-lg font-medium text-gray-900">Yes, invite team</div>
                                    <div class="text-sm text-gray-600">Add team members now</div>
                                </div>
                                <svg x-show="formData.invite_team_now === true"
                                     class="w-6 h-6 text-purple-600 flex-shrink-0"
                                     fill="currentColor"
                                     viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </label>

                        <label class="option-card"
                               :class="{ 'selected': formData.invite_team_now === false }">
                            <input
                                type="radio"
                                name="invite_team_now"
                                :value="false"
                                x-model="formData.invite_team_now"
                                class="sr-only"
                                @change="setTimeout(() => nextQuestion(), 300)"
                            />
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-lg font-medium text-gray-900">I'll do this later</div>
                                    <div class="text-sm text-gray-600">Skip for now</div>
                                </div>
                                <svg x-show="formData.invite_team_now === false"
                                     class="w-6 h-6 text-blue-600 flex-shrink-0"
                                     fill="currentColor"
                                     viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </label>
                    </div>

                    <!-- Team Invites Input (conditional) -->
                    <div x-show="formData.invite_team_now === true" x-transition class="space-y-4">
                        <div class="bg-purple-50 rounded-xl p-6 border-2 border-purple-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Invite team members</h3>

                            <div class="space-y-3">
                                <template x-for="(invite, index) in teamInvites" :key="index">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <input
                                            type="email"
                                            :name="'team_invites[' + index + '][email]'"
                                            x-model="invite.email"
                                            class="modern-input"
                                            placeholder="colleague@company.co.za"
                                        />
                                        <div class="flex items-center space-x-3">
                                            <input
                                                type="text"
                                                :name="'team_invites[' + index + '][name]'"
                                                x-model="invite.name"
                                                class="modern-input flex-1"
                                                placeholder="Name (optional)"
                                            />
                                            <button
                                                type="button"
                                                @click="removeInvite(index)"
                                                x-show="teamInvites.length > 1"
                                                class="p-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <button
                                type="button"
                                @click="addInvite"
                                x-show="teamInvites.length < 20"
                                class="mt-4 w-full px-4 py-3 border-2 border-dashed border-purple-300 rounded-lg text-purple-600 hover:bg-purple-100 transition-colors font-medium">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Another Team Member
                            </button>
                            <p class="mt-3 text-sm text-gray-600">You can invite up to 20 team members</p>
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
function step2Modern() {
    return {
        currentQuestion: 0,
        questions: [
            { field: 'user_role', required: true },
            { field: 'user_type_id', required: true },
            { field: 'has_divisions', required: true },
            { field: 'invite_team_now', required: true }
        ],
        formData: {
            user_role: '{{ old("user_role", $stepData["user_role"] ?? "") }}',
            user_type_id: {{ old('user_type_id', $stepData['user_type_id'] ?? 'null') }},
            has_divisions: {{ old('has_divisions', $stepData['has_divisions'] ?? 'null') }},
            invite_team_now: {{ old('invite_team_now', $stepData['invite_team_now'] ?? 'null') }}
        },
        divisions: @json(old('divisions', $stepData['divisions'] ?? [['name' => '']])),
        teamInvites: @json(old('team_invites', $stepData['team_invites'] ?? [['email' => '', 'name' => '']])),

        init() {
            // Ensure arrays have at least one item
            if (this.divisions.length === 0) {
                this.divisions = [{ name: '' }];
            }
            if (this.teamInvites.length === 0) {
                this.teamInvites = [{ email: '', name: '' }];
            }

            // Keyboard shortcuts
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

        canProceed() {
            const question = this.questions[this.currentQuestion];
            if (!question.required) return true;

            const value = this.formData[question.field];

            // Special handling for divisions
            if (this.currentQuestion === 2 && this.formData.has_divisions === true) {
                return this.divisions.some(d => d.name && d.name.trim() !== '');
            }

            // Special handling for team invites
            if (this.currentQuestion === 3 && this.formData.invite_team_now === true) {
                return this.teamInvites.some(i => i.email && i.email.trim() !== '');
            }

            return value !== null && value !== '' && value !== undefined;
        },

        canSubmit() {
            return this.questions.every((q, index) => {
                const value = this.formData[q.field];

                if (index === 2 && this.formData.has_divisions === true) {
                    return this.divisions.some(d => d.name && d.name.trim() !== '');
                }

                if (index === 3 && this.formData.invite_team_now === true) {
                    return this.teamInvites.some(i => i.email && i.email.trim() !== '');
                }

                return value !== null && value !== '' && value !== undefined;
            });
        },

        nextQuestion() {
            if (!this.canProceed()) return;

            if (this.currentQuestion < this.questions.length - 1) {
                this.currentQuestion++;
                this.saveProgress();
            }
        },

        previousQuestion() {
            if (this.currentQuestion > 0) {
                this.currentQuestion--;
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

        saveProgress() {
            console.log('Progress saved:', this.formData);
        },

        handleSubmit(event) {
            if (!this.canSubmit()) {
                alert('Please complete all required fields');
                return;
            }

            // Clean up empty entries before submitting
            if (!this.formData.has_divisions) {
                this.divisions = [];
            } else {
                this.divisions = this.divisions.filter(d => d.name && d.name.trim() !== '');
            }

            if (!this.formData.invite_team_now) {
                this.teamInvites = [];
            } else {
                this.teamInvites = this.teamInvites.filter(i => i.email && i.email.trim() !== '');
            }

            event.target.submit();
        }
    }
}
</script>
@endpush
