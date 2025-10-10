<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\DocumentType;
use App\Models\DocumentTypeDivision;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentTypeDivisionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DocumentTypeDivision::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'document_type_id' => DocumentType::factory(),
            'division_id' => Division::factory(),
        ];
    }
}
