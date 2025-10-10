<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'trading_name' => fake()->word(),
            'unique_code' => fake()->word(),
            'company_registration_number' => fake()->word(),
            'vat_number' => fake()->word(),
            'tax_reference_number' => fake()->word(),
            'bee_level' => fake()->randomElement(['1', '2', '3', '4', '5', '6', '7', '8', 'non-compliant']),
            'industry_classification' => fake()->word(),
            'company_type' => fake()->randomElement(['pty_ltd', 'public', 'close_corp', 'partnership', 'sole_prop', 'npo', 'trust']),
            'primary_contact_person' => fake()->word(),
            'primary_email' => fake()->word(),
            'primary_phone' => fake()->word(),
            'billing_contact_person' => fake()->word(),
            'billing_email' => fake()->word(),
            'billing_phone' => fake()->word(),
            'physical_address_line1' => fake()->word(),
            'physical_address_line2' => fake()->word(),
            'physical_city' => fake()->word(),
            'physical_province' => fake()->randomElement(['gauteng', 'western_cape', 'kwazulu_natal', 'eastern_cape', 'northern_cape', 'free_state', 'limpopo', 'mpumalanga', 'north_west']),
            'physical_postal_code' => fake()->word(),
            'postal_address_line1' => fake()->word(),
            'postal_address_line2' => fake()->word(),
            'postal_city' => fake()->word(),
            'postal_province' => fake()->word(),
            'postal_code' => fake()->postcode(),
            'subscription_tier' => fake()->randomElement(['starter', 'business', 'enterprise', 'custom']),
            'billing_cycle' => fake()->randomElement(['monthly', 'annually']),
            'subscription_start_date' => fake()->date(),
            'subscription_end_date' => fake()->date(),
            'max_users' => fake()->numberBetween(-10000, 10000),
            'max_subscribers' => fake()->numberBetween(-10000, 10000),
            'max_campaigns_per_month' => fake()->numberBetween(-10000, 10000),
            'max_emails_per_month' => fake()->numberBetween(-10000, 10000),
            'max_sms_per_month' => fake()->numberBetween(-10000, 10000),
            'max_whatsapp_per_month' => fake()->numberBetween(-10000, 10000),
            'allowed_channels' => '{}',
            'default_sender_name' => fake()->word(),
            'default_sender_email' => fake()->word(),
            'default_sender_phone' => fake()->word(),
            'communication_timezone' => fake()->word(),
            'popia_consent_obtained' => fake()->boolean(),
            'popia_consent_date' => fake()->dateTime(),
            'data_retention_period_days' => fake()->numberBetween(-10000, 10000),
            'gdpr_applicable' => fake()->boolean(),
            'privacy_policy_url' => fake()->word(),
            'terms_of_service_url' => fake()->word(),
            'business_hours_start' => fake()->time(),
            'business_hours_end' => fake()->time(),
            'business_days' => '{}',
            'status' => fake()->randomElement(['trial', 'active', 'inactive', 'suspended', 'cancelled']),
            'is_verified' => fake()->boolean(),
            'verification_documents' => '{}',
            'onboarding_completed' => fake()->boolean(),
            'onboarding_step' => fake()->numberBetween(-10000, 10000),
            'monthly_spend_limit' => fake()->randomFloat(0, 0, 9999999999.),
            'current_month_spend' => fake()->randomFloat(0, 0, 9999999999.),
            'currency' => fake()->word(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
            'verified_at' => fake()->dateTime(),
            'last_active_at' => fake()->dateTime(),
            'suspended_at' => fake()->dateTime(),
        ];
    }
}
