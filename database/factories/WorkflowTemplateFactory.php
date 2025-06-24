<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AccessScope;
use App\Models\Division;
use App\Models\StatusType;
use App\Models\WorkflowTemplate;

class WorkflowTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkflowTemplate::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'name' => fake()->name(),
            'division_id' => Division::factory(),
            'description' => fake()->text(),
            'status_id' => fake()->randomNumber(),
            'access_scope_id' => AccessScope::factory(),
            'status_type_id' => StatusType::factory(),
        ];
    }
}
