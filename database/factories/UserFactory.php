<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\;
use App\Models\User;
use App\Models\UserType;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'password' => fake()->password(),
            'user_type_id' => UserType::factory(),
            'tenant_id' => ::factory(),
            'email_verified_at' => fake()->dateTime(),
            'remember_token' => Str::random(10),
            'division_id' => ::factory(),
        ];
    }
}
