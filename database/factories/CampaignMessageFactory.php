<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\;
use App\Models\Campaign;
use App\Models\CampaignMessage;

class CampaignMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CampaignMessage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'subscriber_id' => ::factory(),
            'status' => fake()->randomElement(["pending","sent","delivered","failed","bounced","opened","clicked","unsubscribed"]),
            'sent_at' => fake()->dateTime(),
            'delivered_at' => fake()->dateTime(),
            'opened_at' => fake()->dateTime(),
            'clicked_at' => fake()->dateTime(),
            'error_message' => fake()->text(),
            'provider_message_id' => fake()->word(),
            'provider_response' => '{}',
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
