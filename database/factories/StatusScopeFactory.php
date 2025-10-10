<?php

namespace Database\Factories;

use App\Models\StatusScope;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusScopeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StatusScope::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'label' => fake()->word(),
            'description' => fake()->text(),
        ];
    }
}
