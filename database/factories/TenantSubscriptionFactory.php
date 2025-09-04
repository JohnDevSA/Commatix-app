<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\TenantSubscription;

class TenantSubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TenantSubscription::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'plan_name' => fake()->word(),
            'billing_interval' => fake()->randomElement(["monthly","annually"]),
            'amount' => fake()->randomFloat(0, 0, 9999999999.),
            'currency' => fake()->word(),
            'status' => fake()->randomElement(["active","cancelled","past_due","unpaid","trialing"]),
            'current_period_start' => fake()->dateTime(),
            'current_period_end' => fake()->dateTime(),
            'trial_ends_at' => fake()->dateTime(),
            'cancel_at_period_end' => fake()->boolean(),
            'stripe_subscription_id' => fake()->word(),
            'payfast_subscription_id' => fake()->word(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
