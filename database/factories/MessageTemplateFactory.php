<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\MessageTemplate;
use App\Models\Tenant;

class MessageTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MessageTemplate::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'type' => fake()->randomElement(["email","sms","whatsapp","voice"]),
            'subject' => fake()->word(),
            'content' => fake()->paragraphs(3, true),
            'variables' => '{}',
            'tenant_id' => Tenant::factory(),
            'is_active' => fake()->boolean(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
