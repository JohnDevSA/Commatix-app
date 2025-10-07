<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Milestone;
use App\Models\StatusType;
use App\Models\WorkflowTemplate;

class MilestoneFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Milestone::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'workflow_template_id' => WorkflowTemplate::factory(),
            'name' => fake()->name(),
            'status_id' => fake()->randomNumber(),
            'hint' => fake()->text(),
            'sla_days' => fake()->numberBetween(1, 30),
            'sla_hours' => fake()->numberBetween(0, 23),
            'sla_minutes' => fake()->numberBetween(0, 59),
            'approval_group_id' => fake()->randomNumber(),
            'approval_group_name' => fake()->company() . ' Approval Group',
            'requires_docs' => fake()->boolean(),
            'actions' => '{}',
            'status_type_id' => StatusType::factory(),
        ];
    }
}