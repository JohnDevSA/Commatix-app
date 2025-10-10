<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\DocumentType;
use App\Models\TaskMilestone;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attachment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'task_milestone_id' => TaskMilestone::factory(),
            'document_type_id' => DocumentType::factory(),
            'required' => fake()->boolean(),
            'file_url' => fake()->word(),
            'uploaded_by' => fake()->randomNumber(),
            'uploaded_at' => fake()->dateTime(),
            'user_id' => User::factory(),
        ];
    }
}
