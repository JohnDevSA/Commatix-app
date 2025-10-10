<?php

namespace Database\Factories;

use App\Models\IndustryTemplate;
use App\Models\WorkflowTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class IndustryTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = IndustryTemplate::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'industry' => fake()->randomElement(['finance', 'healthcare', 'retail', 'manufacturing', 'education', 'government', 'nonprofit', 'technology']),
            'description' => fake()->text(),
            'workflow_template_id' => WorkflowTemplate::factory(),
            'compliance_requirements' => '{}',
            'typical_duration_days' => fake()->numberBetween(-10000, 10000),
            'complexity_score' => fake()->numberBetween(-10000, 10000),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
