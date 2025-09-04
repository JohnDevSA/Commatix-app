<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\;
use App\Models\Subscriber;
use App\Models\Tenant;

class SubscriberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscriber::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'whatsapp' => fake()->word(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'tenant_id' => Tenant::factory(),
            'subscriber_list_id' => ::factory(),
            'status' => fake()->randomElement(["active","inactive","unsubscribed","bounced"]),
            'opt_out_date' => fake()->dateTime(),
            'source' => fake()->randomElement(["manual","import","api","web_form"]),
            'tags' => '{}',
            'custom_fields' => '{}',
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
