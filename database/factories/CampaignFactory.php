<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\;
use App\Models\Campaign;
use App\Models\Tenant;
use App\Models\User;

class CampaignFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Campaign::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'type' => fake()->randomElement(["email","sms","whatsapp","voice"]),
            'status' => fake()->randomElement(["draft","scheduled","sending","completed","failed","cancelled","paused"]),
            'tenant_id' => Tenant::factory(),
            'subscriber_list_id' => ::factory(),
            'message_template_id' => ::factory(),
            'scheduled_at' => fake()->dateTime(),
            'started_at' => fake()->dateTime(),
            'completed_at' => fake()->dateTime(),
            'sent_count' => fake()->numberBetween(-10000, 10000),
            'delivered_count' => fake()->numberBetween(-10000, 10000),
            'failed_count' => fake()->numberBetween(-10000, 10000),
            'opened_count' => fake()->numberBetween(-10000, 10000),
            'clicked_count' => fake()->numberBetween(-10000, 10000),
            'unsubscribed_count' => fake()->numberBetween(-10000, 10000),
            'created_by' => fake()->randomNumber(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
            'user_id' => User::factory(),
        ];
    }
}
