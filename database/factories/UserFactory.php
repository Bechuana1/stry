<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => null, // We use magic links, so no password needed
            'remember_token' => Str::random(10),
            'avatar' => fake()->imageUrl(200, 200, 'people', true, 'Faker'),
            'gem_balance' => fake()->numberBetween(0, 100),
            'is_premium' => false,
            'premium_expires_at' => null,
            'reading_streak' => fake()->numberBetween(0, 30),
            'last_streak_update' => fake()->dateTimeBetween('-1 month', 'now'),
            'total_read_seconds' => fake()->numberBetween(0, 36000),
            'is_banned' => false,
        ];
    }

    // State for a user with many Gems
    public function rich(): static
    {
        return $this->state(fn (array $attributes) => [
            'gem_balance' => 200,
        ]);
    }

    // State for a Premium subscriber
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_premium' => true,
            'premium_expires_at' => now()->addMonths(1),
            'gem_balance' => 100,
        ]);
    }
}