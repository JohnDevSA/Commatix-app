<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\;
use App\Models\DataConsentRecord;
use App\Models\Tenant;

class DataConsentRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DataConsentRecord::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'subscriber_id' => ::factory(),
            'consent_type' => fake()->randomElement(["marketing","transactional","analytics","profiling"]),
            'consent_given' => fake()->boolean(),
            'consent_date' => fake()->dateTime(),
            'consent_method' => fake()->randomElement(["web_form","api","manual","import","phone","email"]),
            'consent_source' => fake()->word(),
            'ip_address' => fake()->word(),
            'legal_basis' => fake()->randomElement(["consent","legitimate_interest","contract","legal_obligation","vital_interests","public_task"]),
            'withdrawn_at' => fake()->dateTime(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
