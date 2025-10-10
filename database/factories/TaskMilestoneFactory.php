<?php

namespace Database\Factories;

use App\Models\Milestone;
use App\Models\StatusType;
use App\Models\TaskMilestone;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskMilestoneFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskMilestone::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'milestone_id' => Milestone::factory(),
            'status_id' => fake()->randomNumber(),
            'sla_days' => fake()->numberBetween(-10000, 10000),
            'approval_group_id' => fake()->randomNumber(),
            'requires_docs' => fake()->boolean(),
            'actions' => '{}',
            'completed_at' => fake()->dateTime(),
            'status_type_id' => StatusType::factory(),
        ];
    }
}
