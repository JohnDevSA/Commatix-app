<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\StatusType;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\WorkflowTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'tenant_id' => Tenant::factory(),
            'division_id' => Division::factory(),
            'created_by' => fake()->randomNumber(),
            'assigned_to' => fake()->randomNumber(),
            'status_type_id' => StatusType::factory(),
        ];
    }
}
