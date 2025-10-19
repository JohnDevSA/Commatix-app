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
        Schema::create('onboarding_progress', function (Blueprint $table) {
            $table->id();

            // User relationship (onboarding is per user, not tenant)
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // Current wizard step (1-6)
            $table->integer('current_step')->default(1);

            // Individual step completion tracking
            $table->boolean('step_1_completed')->default(false)
                ->comment('Company Info');
            $table->boolean('step_2_completed')->default(false)
                ->comment('User Role & Team');
            $table->boolean('step_3_completed')->default(false)
                ->comment('Primary Use Case');
            $table->boolean('step_4_completed')->default(false)
                ->comment('SA Integrations');
            $table->boolean('step_5_completed')->default(false)
                ->comment('POPIA Consent');
            $table->boolean('step_6_completed')->default(false)
                ->comment('Pricing Selection');

            // Step data storage (stores form data for each step)
            $table->json('step_data')->nullable()
                ->comment('JSON object with keys: step_1, step_2, step_3, step_4, step_5, step_6');

            // Progress timestamps
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('abandoned_at')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('current_step');
            $table->index(['user_id', 'completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_progress');
    }
};
