<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\;
use App\Models\StatusType;
use App\Models\Task;
use App\Models\WorkflowTemplate;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'reference_number' => fake()->word(),
            'workflow_template_id' => WorkflowTemplate::factory(),
            'status_id' => fake()->randomNumber(),
            'tenant_id' => ::factory(),
            'division_id' => ::factory(),
            'created_by' => fake()->randomNumber(),
            'assigned_to' => fake()->randomNumber(),
            'status_type_id' => StatusType::factory(),
        ];
    }
}
