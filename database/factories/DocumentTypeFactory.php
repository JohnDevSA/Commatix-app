<?php

namespace Database\Factories;

use App\Models\AccessScope;
use App\Models\DocumentType;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DocumentType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'access_scope_id' => AccessScope::factory(),
            'tenant_id' => Tenant::factory(),
        ];
    }
}
