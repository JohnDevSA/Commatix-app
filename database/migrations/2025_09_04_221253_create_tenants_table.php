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
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('trading_name')->nullable();
            $table->string('unique_code')->unique();
            $table->string('company_registration_number')->unique()->nullable();
            $table->string('vat_number')->unique()->nullable();
            $table->string('tax_reference_number')->nullable();
            $table->enum('bee_level', ["1","2","3","4","5","6","7","8","non-compliant"])->nullable();
            $table->string('industry_classification')->nullable();
            $table->enum('company_type', ["pty_ltd","public","close_corp","partnership","sole_prop","npo","trust"])->default('pty_ltd');
            $table->string('primary_contact_person');
            $table->string('primary_email');
            $table->string('primary_phone');
            $table->string('billing_contact_person')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('physical_address_line1');
            $table->string('physical_address_line2')->nullable();
            $table->string('physical_city');
            $table->enum('physical_province', ["gauteng","western_cape","kwazulu_natal","eastern_cape","northern_cape","free_state","limpopo","mpumalanga","north_west"]);
            $table->string('physical_postal_code');
            $table->string('postal_address_line1')->nullable();
            $table->string('postal_address_line2')->nullable();
            $table->string('postal_city')->nullable();
            $table->string('postal_province')->nullable();
            $table->string('postal_code')->nullable();
            $table->enum('subscription_tier', ["starter","business","enterprise","custom"])->default('starter');
            $table->enum('billing_cycle', ["monthly","annually"])->default('monthly');
            $table->date('subscription_start_date')->nullable();
            $table->date('subscription_end_date')->nullable();
            $table->integer('max_users')->default(5);
            $table->integer('max_subscribers')->default(1000);
            $table->integer('max_campaigns_per_month')->default(10);
            $table->integer('max_emails_per_month')->default(5000);
            $table->integer('max_sms_per_month')->default(1000);
            $table->integer('max_whatsapp_per_month')->default(500);
            $table->json('allowed_channels');
            $table->string('default_sender_name')->nullable();
            $table->string('default_sender_email')->nullable();
            $table->string('default_sender_phone')->nullable();
            $table->string('communication_timezone')->default('Africa/Johannesburg');
            $table->boolean('popia_consent_obtained')->default(false);
            $table->timestamp('popia_consent_date')->nullable();
            $table->integer('data_retention_period_days')->default(2555);
            $table->boolean('gdpr_applicable')->default(false);
            $table->string('privacy_policy_url')->nullable();
            $table->string('terms_of_service_url')->nullable();
            $table->time('business_hours_start')->default('08');
            $table->time('business_hours_end')->default('17');
            $table->json('business_days');
            $table->enum('status', ["trial","active","inactive","suspended","cancelled"])->default('trial');
            $table->boolean('is_verified')->default(false);
            $table->json('verification_documents')->nullable();
            $table->boolean('onboarding_completed')->default(false);
            $table->integer('onboarding_step')->default(1);
            $table->decimal('monthly_spend_limit')->nullable();
            $table->decimal('current_month_spend')->default(0.00);
            $table->string('currency')->default('ZAR');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
