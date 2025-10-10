<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\TenantAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantAuditLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TenantAuditLog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'action' => fake()->word(),
            'resource_type' => fake()->word(),
            'resource_id' => fake()->word(),
            'old_values' => '{}',
            'new_values' => '{}',
            'ip_address' => fake()->word(),
            'user_agent' => fake()->text(),
            'created_at' => fake()->dateTime(),
        ];
    }
}
