<?php

namespace Database\Factories;

use App\Models\TaskMilestone;
use App\Models\TaskMilestoneActivityType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskMilestoneActivityTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskMilestoneActivityType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'task_milestone_id' => TaskMilestone::factory(),
            'name' => fake()->name(),
            'icon' => fake()->word(),
            'description' => fake()->text(),
        ];
    }
}
