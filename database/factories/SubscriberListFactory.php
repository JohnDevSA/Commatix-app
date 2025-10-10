<?php

namespace Database\Factories;

use App\Models\SubscriberList;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriberListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubscriberList::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'tenant_id' => Tenant::factory(),
            'total_subscribers' => fake()->numberBetween(-10000, 10000),
            'active_subscribers' => fake()->numberBetween(-10000, 10000),
            'is_public' => fake()->boolean(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
