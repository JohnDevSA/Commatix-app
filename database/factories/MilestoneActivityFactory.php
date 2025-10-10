<?php

namespace Database\Factories;

use App\Models\Milestone;
use App\Models\MilestoneActivity;
use App\Models\MilestoneActivityType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MilestoneActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MilestoneActivity::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'milestone_id' => Milestone::factory(),
            'milestone_activity_type_id' => MilestoneActivityType::factory(),
            'message' => fake()->text(),
            'user_id' => User::factory(),
            'metadata' => '{}',
        ];
    }
}
