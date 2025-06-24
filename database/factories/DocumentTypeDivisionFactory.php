<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\;
use App\Models\DocumentType;
use App\Models\DocumentTypeDivision;

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
            'division_id' => ::factory(),
        ];
    }
}
