<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Models\OnboardingProgress;
use App\Models\Province;
use App\Services\OnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Modern Monday.com-style Onboarding Controller
 *
 * This controller handles the new progressive disclosure onboarding flow.
 * To use this instead of the original, update your routes to point to this controller.
 */
class OnboardingControllerModern extends Controller
{
    /**
     * The onboarding service instance
     */
    protected OnboardingService $onboardingService;

    /**
     * Create a new controller instance
     */
    public function __construct(OnboardingService $onboardingService)
    {
        $this->onboardingService = $onboardingService;
    }

    /**
     * Start or resume onboarding
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        // Check if user already has a completed tenant
        if ($user->tenant_id && $user->tenant?->onboarding_completed) {
            return redirect()->route('onboarding.modern.complete');
        }

        // Get or create onboarding progress
        $progress = OnboardingProgress::firstOrCreate(
            ['user_id' => $user->id],
            ['current_step' => 1]
        );

        // Redirect to current step (use modern routes)
        return redirect()->route('onboarding.modern.step', ['step' => $progress->current_step]);
    }

    /**
     * Show a specific onboarding step (Modern UI)
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
            return redirect()->route('onboarding.complete');
        }

        // Get or create progress
        $progress = OnboardingProgress::firstOrCreate(
            ['user_id' => $user->id],
            ['current_step' => 1]
        );

        // Allow going back, but not skipping ahead
        if ($step > $progress->current_step + 1) {
            return redirect()->route('onboarding.step', ['step' => $progress->current_step]);
        }

        // Get step data from session/database
        $stepData = $this->getStepData($step);

        // Calculate progress
        $progressPercentage = round(($step / 6) * 100);

        // Base view data
        $viewData = [
            'progress' => $progress,
            'currentStep' => $step,
            'totalSteps' => 6,
            'stepData' => $stepData,
            'progressPercentage' => $progressPercentage,
        ];

        // Add step-specific data
        $stepSpecificData = $this->getStepSpecificData($step);
        $viewData = array_merge($viewData, $stepSpecificData);

        // Use modern views
        return view("onboarding.step{$step}-modern", $viewData);
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

        // Save to session for easy retrieval
        $this->saveStepData($step, $validated);

        // Save step data to progress model
        $progress->saveStepData($step, $validated);
        $progress->markStepCompleted($step);

        // Update current step if moving forward
        if ($step >= $progress->current_step) {
            $progress->current_step = min($step + 1, 6);
            $progress->save();
        }

        // Determine next action
        $action = $request->input('action', 'next');

        // If this is the final step, complete onboarding
        if ($step === 6 && $action === 'complete') {
            return $this->completeOnboarding($progress);
        }

        // Redirect to next step (use modern routes)
        $nextStep = $action === 'next' ? min($step + 1, 6) : $step;

        return redirect()->route('onboarding.modern.step', ['step' => $nextStep]);
    }

    /**
     * Show completion celebration page
     */
    public function complete(): View|RedirectResponse
    {
        $user = Auth::user();

        // Ensure user has a tenant
        if (!$user->tenant_id) {
            return redirect()->route('onboarding.index');
        }

        $tenant = $user->tenant;

        // If not completed, redirect to current step
        if (!$tenant->onboarding_completed) {
            $progress = OnboardingProgress::where('user_id', $user->id)->first();
            return redirect()->route('onboarding.step', ['step' => $progress?->current_step ?? 1]);
        }

        return view('onboarding.complete', [
            'tenant' => $tenant,
            'user' => $user,
        ]);
    }

    /**
     * Complete the onboarding process
     */
    protected function completeOnboarding(OnboardingProgress $progress): RedirectResponse
    {
        $user = Auth::user();

        // Use the OnboardingService to handle all tenant creation logic
        $result = $this->onboardingService->completeOnboarding($user, $progress);

        if (!$result['success']) {
            return redirect()->route('onboarding.modern.step', ['step' => 6])
                ->with('error', $result['message']);
        }

        // Save useful info to session for completion page
        $stepData = $progress->getStepData(3) ?? [];
        session([
            'onboarding_step_3.use_case_label' => $this->getUseCaseLabel($stepData['use_case'] ?? null),
            'onboarding_step_6.plan' => $progress->getStepData(6)['plan'] ?? 'professional',
        ]);

        // Redirect to celebration page
        return redirect()->route('onboarding.modern.complete')
            ->with('success', 'Welcome to Commatix! Your account is ready.');
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
                'industry_id' => 'required|exists:industries,id',
                'company_size' => 'required|string',
                'primary_email' => 'required|email|max:255',
                'primary_phone' => 'required|string|max:20',
                'physical_address_line1' => 'required|string|max:255',
                'physical_city' => 'required|string|max:100',
                'physical_province' => 'required|string',
                'physical_postal_code' => 'required|digits:4',
            ],
            2 => [
                'user_role' => 'required|string',
                'user_type_id' => 'required|exists:user_types,id',
                'has_divisions' => 'nullable|boolean',
                'divisions' => 'nullable|array',
                'divisions.*.name' => 'nullable|string|max:255',
                'invite_team_now' => 'nullable|boolean',
                'team_invites' => 'nullable|array',
                'team_invites.*.email' => 'nullable|email|max:255',
                'team_invites.*.name' => 'nullable|string|max:255',
            ],
            3 => [
                'use_case' => 'required|string',
                'workflow_template_ids' => 'nullable|array',
                'workflow_template_ids.*' => 'exists:workflow_templates,id',
            ],
            4 => [
                'integrations' => 'nullable|array',
                'integrations.*' => 'string',
            ],
            5 => [
                'accept_terms' => 'required|accepted',
                'accept_privacy' => 'required|accepted',
                'marketing_consent' => 'nullable|boolean',
            ],
            6 => [
                'plan' => 'required|string|in:starter,professional,enterprise',
                'billing_cycle' => 'required|string|in:monthly,annual',
            ],
            default => [],
        };

        return $request->validate($rules);
    }

    /**
     * Get step-specific data for views (using cached models via OnboardingService)
     */
    protected function getStepSpecificData(int $step): array
    {
        return match ($step) {
            1 => [
                'provinces' => $this->onboardingService->getProvinces(),
                'industries' => $this->onboardingService->getIndustries(),
            ],
            2 => [
                'userTypes' => $this->getUserTypes(),
            ],
            3 => [
                'workflowTemplates' => $this->getWorkflowTemplates(),
            ],
            default => [],
        };
    }

    /**
     * Save step data to session
     */
    protected function saveStepData(int $step, array $data): void
    {
        session(["onboarding_step_{$step}" => $data]);
    }

    /**
     * Get step data from session
     */
    protected function getStepData(int $step): array
    {
        return session("onboarding_step_{$step}", []);
    }

    /**
     * Get human-readable use case label
     */
    protected function getUseCaseLabel(?string $useCase): string
    {
        return match ($useCase) {
            'email_marketing' => 'Email Marketing',
            'sms_campaigns' => 'SMS Campaigns',
            'workflow_automation' => 'Workflow Automation',
            'task_management' => 'Task Management',
            'multi_channel' => 'Multi-Channel Communication',
            default => 'Use case configured',
        };
    }

    /**
     * Get user types with fallback (using OnboardingService pattern)
     */
    protected function getUserTypes()
    {
        try {
            $userTypes = \DB::table('user_types')->orderBy('name')->get();
            if ($userTypes->count() > 0) {
                return $userTypes;
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to load user types', ['error' => $e->getMessage()]);
        }

        // Fallback user types
        return collect([
            (object)['id' => 1, 'name' => 'Manager'],
            (object)['id' => 2, 'name' => 'Team Member'],
            (object)['id' => 3, 'name' => 'Administrator'],
            (object)['id' => 4, 'name' => 'Consultant'],
        ]);
    }

    /**
     * Get workflow templates with fallback
     */
    protected function getWorkflowTemplates()
    {
        try {
            $templates = \DB::table('workflow_templates')
                ->where('is_published', true)
                ->orderBy('name')
                ->get();
            if ($templates->count() > 0) {
                return $templates;
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to load workflow templates', ['error' => $e->getMessage()]);
        }

        // Fallback workflow templates
        return collect([
            (object)['id' => 1, 'name' => 'Client Onboarding'],
            (object)['id' => 2, 'name' => 'Project Management'],
            (object)['id' => 3, 'name' => 'Invoice Approval'],
            (object)['id' => 4, 'name' => 'Employee Onboarding'],
        ]);
    }
}
