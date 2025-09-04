<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\;
use App\Models\Tenant;
use App\Models\TenantAuditLog;

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
            'user_id' => ::factory(),
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
