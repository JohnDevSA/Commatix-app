<?php

namespace Database\Factories;

use App\Models\WorkflowTemplateTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowTemplateTagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkflowTemplateTag::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'color' => fake()->word(),
            'icon' => fake()->word(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
