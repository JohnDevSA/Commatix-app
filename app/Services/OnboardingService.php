<?php

namespace App\Services;

use App\Events\OnboardingCompleted;
use App\Models\Industry;
use App\Models\OnboardingProgress;
use App\Models\Province;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stancl\Tenancy\Facades\Tenancy;

/**
 * OnboardingService
 *
 * Handles the complete onboarding process for new tenants.
 * This service ensures proper tenant creation, database setup,
 * admin user creation, and tenant initialization.
 *
 * @package App\Services
 */
class OnboardingService
{
    /**
     * Complete the onboarding process and create tenant
     *
     * @param User $user The user completing onboarding
     * @param OnboardingProgress $progress The onboarding progress record
     * @return array{success: bool, tenant: Tenant|null, message: string}
     */
    public function completeOnboarding(User $user, OnboardingProgress $progress): array
    {
        DB::beginTransaction();

        try {
            // Get all step data
            $allStepData = $this->gatherAllStepData($progress);

            // Validate we have required data
            $validation = $this->validateStepData($allStepData);
            if (!$validation['valid']) {
                DB::rollBack();
                return [
                    'success' => false,
                    'tenant' => null,
                    'message' => 'Missing required onboarding data: ' . $validation['message'],
                ];
            }

            // Create the tenant
            $tenant = $this->createTenant($allStepData, $user);

            // Assign tenant to user
            $user->tenant_id = $tenant->id;
            $user->save();

            // Initialize tenant database and create admin user
            $this->initializeTenantDatabase($tenant, $user, $allStepData);

            // Mark progress as complete
            $progress->update([
                'completed_at' => now(),
                'current_step' => 6,
            ]);

            // Fire completion event
            event(new OnboardingCompleted($tenant, $user, $progress));

            DB::commit();

            Log::info('Onboarding completed successfully', [
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
            ]);

            return [
                'success' => true,
                'tenant' => $tenant,
                'message' => 'Onboarding completed successfully!',
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Onboarding completion failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'tenant' => null,
                'message' => 'Onboarding failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Gather all step data from progress
     *
     * @param OnboardingProgress $progress
     * @return array
     */
    protected function gatherAllStepData(OnboardingProgress $progress): array
    {
        return [
            'step_1' => $progress->getStepData(1) ?? [],
            'step_2' => $progress->getStepData(2) ?? [],
            'step_3' => $progress->getStepData(3) ?? [],
            'step_4' => $progress->getStepData(4) ?? [],
            'step_5' => $progress->getStepData(5) ?? [],
            'step_6' => $progress->getStepData(6) ?? [],
        ];
    }

    /**
     * Validate that all required step data exists
     *
     * @param array $stepData
     * @return array{valid: bool, message: string}
     */
    protected function validateStepData(array $stepData): array
    {
        $step1 = $stepData['step_1'] ?? [];

        // Required fields from Step 1
        $requiredFields = [
            'company_name',
            'company_registration_number',
            'industry_id',
            'company_size',
            'primary_email',
            'primary_phone',
            'physical_address_line1',
            'physical_city',
            'physical_province',
            'physical_postal_code',
        ];

        foreach ($requiredFields as $field) {
            if (empty($step1[$field])) {
                return [
                    'valid' => false,
                    'message' => "Missing required field: {$field}",
                ];
            }
        }

        // Validate Step 5 (POPIA consent)
        $step5 = $stepData['step_5'] ?? [];
        if (empty($step5['accept_terms']) || empty($step5['accept_privacy'])) {
            return [
                'valid' => false,
                'message' => 'POPIA consent is required',
            ];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Create tenant from onboarding data
     *
     * @param array $stepData
     * @param User $user
     * @return Tenant
     */
    protected function createTenant(array $stepData, User $user): Tenant
    {
        $step1 = $stepData['step_1'];
        $step2 = $stepData['step_2'] ?? [];
        $step3 = $stepData['step_3'] ?? [];
        $step4 = $stepData['step_4'] ?? [];
        $step5 = $stepData['step_5'] ?? [];
        $step6 = $stepData['step_6'] ?? [];

        // Get industry code (if industry_id provided, fetch the code)
        $industryCode = null;
        if (!empty($step1['industry_id'])) {
            $industry = Industry::findCached($step1['industry_id']);
            $industryCode = $industry?->code;
        }

        // Create tenant
        $tenant = Tenant::create([
            'id' => 'tenant_' . Str::uuid(),
            'name' => $step1['company_name'],
            'trading_name' => $step1['trading_name'] ?? null,
            'unique_code' => $this->generateUniqueCode($step1['company_name']),
            'company_registration_number' => $step1['company_registration_number'],
            'vat_number' => $step1['vat_number'] ?? null,
            'tax_reference_number' => $step1['tax_reference_number'] ?? null,
            'bee_level' => $step1['bbee_level'] ?? null,
            'industry_classification' => $industryCode,
            'company_type' => $step1['company_type'] ?? 'pty_ltd',
            'primary_contact_person' => $step1['primary_contact_person'] ?? $user->name,
            'primary_email' => $step1['primary_email'],
            'primary_phone' => $step1['primary_phone'],
            'billing_email' => $step1['primary_email'], // Default to primary email
            'billing_phone' => $step1['primary_phone'], // Default to primary phone
            'physical_address_line1' => $step1['physical_address_line1'],
            'physical_address_line2' => $step1['physical_address_line2'] ?? null,
            'physical_city' => $step1['physical_city'],
            'physical_province' => $step1['physical_province'],
            'physical_postal_code' => $step1['physical_postal_code'],
            'postal_address_line1' => $step1['postal_address_line1'] ?? $step1['physical_address_line1'],
            'postal_address_line2' => $step1['postal_address_line2'] ?? $step1['physical_address_line2'],
            'postal_city' => $step1['postal_city'] ?? $step1['physical_city'],
            'postal_province' => $step1['postal_province'] ?? $step1['physical_province'],
            'postal_code' => $step1['postal_postal_code'] ?? $step1['physical_postal_code'],
            'subscription_tier' => $step6['plan'] ?? 'professional',
            'billing_cycle' => $step6['billing_cycle'] ?? 'monthly',
            'subscription_start_date' => now(),
            'subscription_end_date' => now()->addDays(30), // 30-day trial
            'max_users' => $this->getMaxUsersByPlan($step6['plan'] ?? 'professional'),
            'max_subscribers' => $this->getMaxSubscribersByPlan($step6['plan'] ?? 'professional'),
            'allowed_channels' => ['email', 'sms'],
            'communication_timezone' => 'Africa/Johannesburg',
            'popia_consent_obtained' => !empty($step5['accept_privacy']),
            'popia_consent_date' => now(),
            'data_retention_period_days' => 365,
            'status' => 'active',
            'is_verified' => false,
            'onboarding_completed' => true,
            'onboarding_step' => 6,
            'onboarding_status' => 'completed',
            'onboarding_started_at' => $user->created_at ?? now(),
            'onboarding_completed_at' => now(),
            'selected_use_case' => $step3['use_case'] ?? null,
            'selected_integrations' => $step4['integrations'] ?? [],
            'setup_wizard_data' => [
                'user_role' => $step2['user_role'] ?? null,
                'user_type_id' => $step2['user_type_id'] ?? null,
                'has_divisions' => $step2['has_divisions'] ?? false,
                'workflow_template_ids' => $step3['workflow_template_ids'] ?? [],
                'marketing_consent' => $step5['marketing_consent'] ?? false,
            ],
            'currency' => 'ZAR',
        ]);

        return $tenant;
    }

    /**
     * Initialize tenant database and create admin user
     *
     * @param Tenant $tenant
     * @param User $user
     * @param array $stepData
     * @return void
     */
    protected function initializeTenantDatabase(Tenant $tenant, User $user, array $stepData): void
    {
        // Run tenant migrations
        $tenant->run(function ($tenant) use ($user, $stepData) {
            // Migrate tenant database
            \Artisan::call('tenants:migrate', ['--tenants' => [$tenant->id]]);

            // Initialize tenant context
            Tenancy::initialize($tenant);

            // Create tenant admin user
            $this->createTenantAdminUser($tenant, $user, $stepData);

            // Create divisions if specified
            $this->createInitialDivisions($tenant, $stepData);

            // Send team invites if specified
            $this->sendTeamInvites($tenant, $stepData);
        });
    }

    /**
     * Create tenant admin user
     *
     * @param Tenant $tenant
     * @param User $centralUser
     * @param array $stepData
     * @return void
     */
    protected function createTenantAdminUser(Tenant $tenant, User $centralUser, array $stepData): void
    {
        $step2 = $stepData['step_2'] ?? [];

        // Create user in tenant database
        $tenantUser = \App\Models\User::create([
            'name' => $centralUser->name,
            'email' => $centralUser->email,
            'password' => $centralUser->password, // Copy hashed password
            'email_verified_at' => $centralUser->email_verified_at,
            'tenant_id' => $tenant->id,
            'user_type_id' => $step2['user_type_id'] ?? null,
            'is_active' => true,
        ]);

        // Assign tenant admin role
        $tenantUser->assignRole('tenant_admin');

        Log::info('Tenant admin user created', [
            'tenant_id' => $tenant->id,
            'user_id' => $tenantUser->id,
            'email' => $tenantUser->email,
        ]);
    }

    /**
     * Create initial divisions if specified
     *
     * @param Tenant $tenant
     * @param array $stepData
     * @return void
     */
    protected function createInitialDivisions(Tenant $tenant, array $stepData): void
    {
        $step2 = $stepData['step_2'] ?? [];

        if (empty($step2['has_divisions']) || empty($step2['divisions'])) {
            return;
        }

        foreach ($step2['divisions'] as $divisionData) {
            if (empty($divisionData['name'])) {
                continue;
            }

            \App\Models\Division::create([
                'tenant_id' => $tenant->id,
                'name' => $divisionData['name'],
                'code' => Str::slug($divisionData['name']),
                'is_active' => true,
            ]);
        }

        Log::info('Initial divisions created', [
            'tenant_id' => $tenant->id,
            'division_count' => count($step2['divisions']),
        ]);
    }

    /**
     * Send team invites if specified
     *
     * @param Tenant $tenant
     * @param array $stepData
     * @return void
     */
    protected function sendTeamInvites(Tenant $tenant, array $stepData): void
    {
        $step2 = $stepData['step_2'] ?? [];

        if (empty($step2['invite_team_now']) || empty($step2['team_invites'])) {
            return;
        }

        foreach ($step2['team_invites'] as $inviteData) {
            if (empty($inviteData['email'])) {
                continue;
            }

            // TODO: Implement team invite logic
            // This would typically:
            // 1. Create invitation record
            // 2. Send invitation email
            // 3. Generate unique invitation token

            Log::info('Team invite queued', [
                'tenant_id' => $tenant->id,
                'email' => $inviteData['email'],
            ]);
        }
    }

    /**
     * Generate unique code for tenant
     *
     * @param string $companyName
     * @return string
     */
    protected function generateUniqueCode(string $companyName): string
    {
        $baseCode = Str::upper(Str::substr(Str::slug($companyName, ''), 0, 6));
        $counter = 1;
        $code = $baseCode;

        while (Tenant::where('unique_code', $code)->exists()) {
            $code = $baseCode . $counter;
            $counter++;
        }

        return $code;
    }

    /**
     * Get max users by subscription plan
     *
     * @param string $plan
     * @return int
     */
    protected function getMaxUsersByPlan(string $plan): int
    {
        return match($plan) {
            'starter' => 5,
            'professional' => 25,
            'enterprise' => 9999,
            default => 25,
        };
    }

    /**
     * Get max subscribers by subscription plan
     *
     * @param string $plan
     * @return int
     */
    protected function getMaxSubscribersByPlan(string $plan): int
    {
        return match($plan) {
            'starter' => 1000,
            'professional' => 10000,
            'enterprise' => 999999,
            default => 10000,
        };
    }

    /**
     * Get cached provinces for onboarding
     *
     * @return array
     */
    public function getProvinces(): array
    {
        try {
            return Province::getSelectOptions();
        } catch (\Exception $e) {
            Log::warning('Failed to load provinces from database', ['error' => $e->getMessage()]);
            return $this->getFallbackProvinces();
        }
    }

    /**
     * Get cached industries for onboarding
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getIndustries()
    {
        try {
            $industries = Industry::getAllActiveCached();

            // If empty, use fallback
            if ($industries->isEmpty()) {
                return collect($this->getFallbackIndustries());
            }

            return $industries;
        } catch (\Exception $e) {
            Log::warning('Failed to load industries from database', ['error' => $e->getMessage()]);
            return collect($this->getFallbackIndustries());
        }
    }

    /**
     * Fallback provinces if database unavailable
     *
     * @return array
     */
    protected function getFallbackProvinces(): array
    {
        return [
            'EC' => 'Eastern Cape',
            'FS' => 'Free State',
            'GP' => 'Gauteng',
            'KZN' => 'KwaZulu-Natal',
            'LP' => 'Limpopo',
            'MP' => 'Mpumalanga',
            'NC' => 'Northern Cape',
            'NW' => 'North West',
            'WC' => 'Western Cape',
        ];
    }

    /**
     * Fallback industries if database unavailable
     *
     * @return array
     */
    protected function getFallbackIndustries(): array
    {
        return [
            (object)['id' => 1, 'name' => 'Technology & IT', 'icon' => '💻'],
            (object)['id' => 2, 'name' => 'Healthcare & Medical', 'icon' => '🏥'],
            (object)['id' => 3, 'name' => 'Finance & Banking', 'icon' => '🏦'],
            (object)['id' => 4, 'name' => 'Legal Services', 'icon' => '⚖️'],
            (object)['id' => 5, 'name' => 'Manufacturing', 'icon' => '🏭'],
            (object)['id' => 6, 'name' => 'Retail & E-commerce', 'icon' => '🛍️'],
            (object)['id' => 7, 'name' => 'Construction & Engineering', 'icon' => '🏗️'],
            (object)['id' => 8, 'name' => 'Education & Training', 'icon' => '🎓'],
            (object)['id' => 9, 'name' => 'Hospitality & Tourism', 'icon' => '🏨'],
            (object)['id' => 10, 'name' => 'Transportation & Logistics', 'icon' => '🚚'],
            (object)['id' => 11, 'name' => 'Professional Services', 'icon' => '💼'],
            (object)['id' => 12, 'name' => 'Agriculture & Farming', 'icon' => '🌾'],
            (object)['id' => 13, 'name' => 'Media & Entertainment', 'icon' => '🎬'],
            (object)['id' => 14, 'name' => 'Real Estate', 'icon' => '🏠'],
            (object)['id' => 15, 'name' => 'Other', 'icon' => '📊'],
        ];
    }
}
