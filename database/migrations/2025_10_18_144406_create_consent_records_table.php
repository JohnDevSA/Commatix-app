<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates consent_records table for POPIA compliance tracking.
     * Must retain records for minimum 5 years per POPIA requirements.
     */
    public function up(): void
    {
        Schema::create('consent_records', function (Blueprint $table) {
            $table->id();

            // Tenant relationship
            $table->string('tenant_id');
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onDelete('cascade');

            // User relationship (nullable for pre-registration consent)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            // Consent type - specific, unbundled consent per POPIA requirements
            $table->enum('consent_type', [
                'processing',           // Mandatory - process personal information
                'marketing',            // Optional - receive marketing communications
                'profiling',            // Optional - automated decision making/profiling
                'third_party_sharing',  // Optional - share data with third parties
            ])->comment('Specific consent type - must be unbundled per POPIA');

            // Consent status
            $table->boolean('granted')->default(true)
                ->comment('True if granted, false if denied or withdrawn');

            // Full consent text shown to user (required for audit trail)
            $table->text('consent_text')
                ->comment('Exact wording shown to user at time of consent - required for POPIA audit trail');

            // Legal audit trail - capture context of consent
            $table->string('ip_address', 45)->nullable()
                ->comment('IPv4 or IPv6 address where consent was given');

            $table->text('user_agent')->nullable()
                ->comment('Browser/device user agent for audit trail');

            // Consent timestamps
            $table->timestamp('consented_at')
                ->comment('When consent was given');

            $table->timestamp('withdrawn_at')->nullable()
                ->comment('When consent was withdrawn (if applicable)');

            // Withdrawal details
            $table->text('withdrawal_reason')->nullable()
                ->comment('User-provided reason for withdrawal (optional)');

            $table->timestamps();

            // Indexes for performance and compliance queries
            $table->index('tenant_id');
            $table->index('user_id');
            $table->index(['user_id', 'consent_type', 'granted']);
            $table->index('consented_at');
            $table->index('withdrawn_at');

            // Composite index for finding active consents
            $table->index(['user_id', 'consent_type', 'granted', 'withdrawn_at'], 'idx_active_consents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consent_records');
    }
};
