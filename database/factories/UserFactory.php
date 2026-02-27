<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hu_name' => fake()->name(),
            'hu_email' => fake()->unique()->safeEmail(),
            'hu_email_verified_at' => now(),
            'hu_password' => static::$password ??= Hash::make('password'),
            'hu_role' => 'student',
            'hu_phone' => fake()->numerify('01########'),
            'hu_verification_status' => 'approved',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'hu_email_verified_at' => null,
        ]);
    }
}
