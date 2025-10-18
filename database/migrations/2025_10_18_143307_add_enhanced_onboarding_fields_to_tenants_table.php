<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Enhanced onboarding status tracking
            $table->enum('onboarding_status', [
                'pending',       // Registration complete, awaiting database provisioning
                'provisioning',  // Database being created
                'ready',         // Database ready, wizard not started
                'in_progress',   // Wizard started but not completed
                'completed',     // Onboarding fully completed
                'abandoned',     // Started but abandoned (for recovery flows)
            ])->default('pending')->after('onboarding_step');

            // Onboarding timestamps for analytics and recovery
            $table->timestamp('onboarding_started_at')->nullable()->after('onboarding_status');
            $table->timestamp('onboarding_completed_at')->nullable()->after('onboarding_started_at');

            // Tenant's primary use case selection (from wizard step 3)
            $table->string('selected_use_case')->nullable()->after('onboarding_completed_at')
                ->comment('Primary use case: email_marketing, sms_campaigns, workflow_automation, task_management, multi_channel');

            // Selected integrations during onboarding (from wizard step 4)
            $table->json('selected_integrations')->nullable()->after('selected_use_case')
                ->comment('Array of selected integrations: payfast, yoco, sage, xero, fnb, standard_bank, etc.');

            // Temporary wizard progress data (cleared after completion)
            $table->json('setup_wizard_data')->nullable()->after('selected_integrations')
                ->comment('Temporary storage for wizard step data, cleared after onboarding completion');

            // Add index for onboarding status queries (e.g., finding abandoned onboardings)
            $table->index('onboarding_status', 'idx_tenants_onboarding_status');
            $table->index('onboarding_started_at', 'idx_tenants_onboarding_started');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_tenants_onboarding_status');
            $table->dropIndex('idx_tenants_onboarding_started');

            // Drop columns in reverse order
            $table->dropColumn([
                'setup_wizard_data',
                'selected_integrations',
                'selected_use_case',
                'onboarding_completed_at',
                'onboarding_started_at',
                'onboarding_status',
            ]);
        });
    }
};
