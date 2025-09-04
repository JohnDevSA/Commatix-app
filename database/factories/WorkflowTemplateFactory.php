<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\;
use App\Models\Division;
use App\Models\StatusType;
use App\Models\User;
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
            'access_scope_id' => ::factory(),
            'template_type' => fake()->randomElement(["system","industry","custom","copied"]),
            'parent_template_id' => ::factory(),
            'industry_category' => fake()->word(),
            'template_version' => fake()->word(),
            'created_by' => fake()->randomNumber(),
            'is_public' => fake()->boolean(),
            'is_system_template' => fake()->boolean(),
            'tenant_id' => fake()->word(),
            'usage_count' => fake()->numberBetween(-10000, 10000),
            'last_used_at' => fake()->dateTime(),
            'tags' => '{}',
            'estimated_duration_days' => fake()->numberBetween(-10000, 10000),
            'complexity_level' => fake()->randomElement(["simple","medium","complex"]),
            'is_customizable' => fake()->boolean(),
            'locked_milestones' => '{}',
            'required_roles' => '{}',
            'is_published' => fake()->boolean(),
            'published_at' => fake()->dateTime(),
            'change_log' => fake()->text(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
            'status_type_id' => StatusType::factory(),
            'user_id' => User::factory(),
        ];
    }
}
