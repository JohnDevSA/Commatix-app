<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\TaskMilestone;
use App\Models\TaskMilestoneActivityType;

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
