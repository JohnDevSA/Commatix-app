<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkflowTemplate;
use App\Models\WorkflowTemplateUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowTemplateUsageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkflowTemplateUsage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_template_id' => WorkflowTemplate::factory(),
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'action' => fake()->randomElement(['copied', 'used', 'modified', 'published']),
            'metadata' => '{}',
            'created_at' => fake()->dateTime(),
        ];
    }
}
