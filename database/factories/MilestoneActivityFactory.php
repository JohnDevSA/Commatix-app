<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\;
use App\Models\Milestone;
use App\Models\MilestoneActivity;

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
            'milestone_activity_type_id' => ::factory(),
            'message' => fake()->text(),
            'user_id' => ::factory(),
            'metadata' => '{}',
        ];
    }
}
