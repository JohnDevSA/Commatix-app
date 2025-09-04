<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\;
use App\Models\WorkflowTemplate;
use App\Models\WorkflowTemplateUsage;

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
            'tenant_id' => ::factory(),
            'user_id' => ::factory(),
            'action' => fake()->randomElement(["copied","used","modified","published"]),
            'metadata' => '{}',
            'created_at' => fake()->dateTime(),
        ];
    }
}
