<?php

namespace Database\Factories;

use App\Models\StatusType;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StatusType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
        ];
    }
}
