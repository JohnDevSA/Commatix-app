<?php

namespace Database\Factories;

use App\Models\AccessScope;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessScopeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccessScope::class;

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
