<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\TenantUsage;

class TenantUsageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TenantUsage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'period_start' => fake()->dateTime(),
            'period_end' => fake()->dateTime(),
            'emails_sent' => fake()->numberBetween(-10000, 10000),
            'sms_sent' => fake()->numberBetween(-10000, 10000),
            'whatsapp_sent' => fake()->numberBetween(-10000, 10000),
            'voice_calls' => fake()->numberBetween(-10000, 10000),
            'storage_used_mb' => fake()->randomFloat(0, 0, 9999999999.),
            'api_calls' => fake()->numberBetween(-10000, 10000),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
