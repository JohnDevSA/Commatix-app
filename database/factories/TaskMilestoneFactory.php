<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Milestone;
use App\Models\StatusType;
use App\Models\TaskMilestone;

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
            'status_id' => StatusType::factory(),
            'sla_days' => fake()->numberBetween(-10000, 10000),
            'approval_group_id' => fake()->randomNumber(),
            'requires_docs' => fake()->boolean(),
            'actions' => '{}',
            'completed_at' => fake()->dateTime(),
            'status_type_id' => StatusType::factory(),
        ];
    }
}
