<?php

namespace App\Http\Controllers;

use App\Events\OnboardingCompleted;
use App\Models\OnboardingProgress;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OnboardingController extends Controller
{
    /**
     * Start or resume onboarding
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        // Check if user already has a completed tenant
        if ($user->tenant_id && $user->tenant?->onboarding_completed) {
            return redirect()->to('/dashboard');
        }

        // Get or create onboarding progress
        $progress = OnboardingProgress::firstOrCreate(
            ['user_id' => $user->id],
            ['current_step' => 1]
        );

        // Redirect to current step
        return redirect()->route('onboarding.step', ['step' => $progress->current_step]);
    }

    /**
     * Show a specific onboarding step
     */
    public function showStep(int $step): View|RedirectResponse
    {
        $user = Auth::user();

        // Validate step number
        if ($step < 1 || $step > 6) {
            return redirect()->route('onboarding.index');
        }

        // Check if user already completed onboarding
        if ($user->tenant_id && $user->tenant?->onboarding_completed) {
            return redirect()->to('/dashboard');
        }

        // Get or create progress
        $progress = OnboardingProgress::firstOrCreate(
            ['user_id' => $user->id],
            ['current_step' => 1]
        );

        // Don't allow skipping steps
        if ($step > $progress->current_step + 1) {
            return redirect()->route('onboarding.step', ['step' => $progress->current_step]);
        }

        // Get step data
        $stepData = $progress->getStepData($step) ?? [];

        // Load any necessary data for the step
        $viewData = [
            'progress' => $progress,
            'currentStep' => $step,
            'stepData' => $stepData,
            'completionPercentage' => $progress->getCompletionPercentage(),
        ];

        // Add step-specific data
        switch ($step) {
            case 1:
                $viewData['provinces'] = $this->getSAProvinces();
                $viewData['industries'] = DB::table('industries')->orderBy('name')->get();
                break;
            case 2:
                $viewData['userTypes'] = DB::table('user_types')->orderBy('name')->get();
                break;
            case 3:
                $viewData['workflowTemplates'] = DB::table('workflow_templates')
                    ->where('is_published', true)
                    ->orderBy('name')
                    ->get();
                break;
            case 6:
                // Pricing data can be loaded here
                break;
        }

        return view("onboarding.step{$step}", $viewData);
    }

    /**
     * Process a step submission
     */
    public function processStep(Request $request, int $step): RedirectResponse
    {
        $user = Auth::user();

        // Get progress
        $progress = OnboardingProgress::where('user_id', $user->id)->firstOrFail();

        // Validate based on step
        $validated = $this->validateStep($request, $step);

        // Save step data
        $progress->saveStepData($step, $validated);
        $progress->markStepCompleted($step);

        // Update current step if moving forward
        if ($step >= $progress->current_step) {
            $progress->current_step = min($step + 1, 6);
            $progress->save();
        }

        // Check if all steps are completed
        if ($progress->isComplete()) {
            return $this->completeOnboarding($progress);
        }

        // Redirect to next step or stay on current
        $nextStep = $request->input('action') === 'next' ? $step + 1 : $step;
        return redirect()->route('onboarding.step', ['step' => $nextStep])
            ->with('success', 'Progress saved!');
    }

    /**
     * Complete the onboarding process
     */
    protected function completeOnboarding(OnboardingProgress $progress): RedirectResponse
    {
        $user = Auth::user();

        DB::beginTransaction();
        try {
            // Create tenant from step 1 data
            $step1Data = $progress->getStepData(1);

            $tenant = Tenant::create([
                'name' => $step1Data['company_name'],
                'trading_name' => $step1Data['trading_name'] ?? null,
                'company_registration_number' => $step1Data['company_registration_number'],
                'vat_number' => $step1Data['vat_number'] ?? null,
                'tax_reference_number' => $step1Data['tax_reference_number'] ?? null,
                'industry_id' => $step1Data['industry_id'],
                'company_type' => $step1Data['company_type'],
                'bbee_level' => $step1Data['bbee_level'] ?? null,
                'company_size' => $step1Data['company_size'],
                'primary_contact_person' => $step1Data['primary_contact_person'],
                'primary_email' => $step1Data['primary_email'],
                'primary_phone' => $step1Data['primary_phone'],
                'physical_address_line1' => $step1Data['physical_address_line1'],
                'physical_address_line2' => $step1Data['physical_address_line2'] ?? null,
                'physical_city' => $step1Data['physical_city'],
                'physical_province' => $step1Data['physical_province'],
                'physical_postal_code' => $step1Data['physical_postal_code'],
                'postal_address_line1' => $step1Data['postal_address_line1'],
                'postal_address_line2' => $step1Data['postal_address_line2'] ?? null,
                'postal_city' => $step1Data['postal_city'],
                'postal_province' => $step1Data['postal_province'],
                'postal_postal_code' => $step1Data['postal_postal_code'],
                'onboarding_completed' => true,
                'onboarding_completed_at' => now(),
            ]);

            // Assign tenant to user
            $user->tenant_id = $tenant->id;
            $user->save();

            // Fire completion event
            event(new OnboardingCompleted($tenant, $user, $progress));

            DB::commit();

            return redirect()->to('/dashboard')
                ->with('success', 'Welcome to Commatix! Your workspace is ready.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Onboarding completion failed: ' . $e->getMessage());
            return redirect()->route('onboarding.step', ['step' => 6])
                ->with('error', 'There was an error completing your setup. Please try again.');
        }
    }

    /**
     * Validate step data
     */
    protected function validateStep(Request $request, int $step): array
    {
        $rules = match ($step) {
            1 => [
                'company_name' => 'required|string|max:255',
                'trading_name' => 'nullable|string|max:255',
                'company_registration_number' => 'required|regex:/^\d{4}\/\d{6}\/\d{2}$/',
                'vat_number' => 'nullable|digits:10',
                'tax_reference_number' => 'nullable|string|max:20',
                'industry_id' => 'required|exists:industries,id',
                'company_type' => 'required|string',
                'bbee_level' => 'nullable|string',
                'company_size' => 'required|string',
                'primary_contact_person' => 'required|string|max:255',
                'primary_email' => 'required|email|max:255',
                'primary_phone' => 'required|string|max:20',
                'physical_address_line1' => 'required|string|max:255',
                'physical_address_line2' => 'nullable|string|max:255',
                'physical_city' => 'required|string|max:100',
                'physical_province' => 'required|string',
                'physical_postal_code' => 'required|digits:4',
                'same_as_physical' => 'boolean',
                'postal_address_line1' => 'required|string|max:255',
                'postal_address_line2' => 'nullable|string|max:255',
                'postal_city' => 'required|string|max:100',
                'postal_province' => 'required|string',
                'postal_postal_code' => 'required|digits:4',
            ],
            2 => [
                'user_role' => 'required|string',
                'user_type_id' => 'required|exists:user_types,id',
                'has_divisions' => 'boolean',
                'divisions' => 'nullable|array',
                'divisions.*.name' => 'required|string|max:255',
                'invite_team_now' => 'boolean',
                'team_invites' => 'nullable|array',
                'team_invites.*.email' => 'required|email|max:255',
                'team_invites.*.name' => 'nullable|string|max:255',
            ],
            3 => [
                'use_case' => 'required|string',
                'workflow_template_ids' => 'nullable|array',
                'workflow_template_ids.*' => 'exists:workflow_templates,id',
            ],
            4 => [
                'configure_payment_now' => 'boolean',
                'accounting_software' => 'nullable|string',
                'accounting_software_other' => 'nullable|string|max:255',
            ],
            5 => [
                'popia_consent' => 'required|accepted',
                'marketing_consent' => 'boolean',
            ],
            6 => [
                'selected_plan' => 'required|string|in:starter,professional,enterprise',
                'billing_cycle' => 'required|string|in:monthly,annual',
            ],
            default => [],
        };

        return $request->validate($rules);
    }

    /**
     * Get South African provinces from database
     */
    protected function getSAProvinces(): array
    {
        return DB::table('sa_provinces')
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();
    }
}
