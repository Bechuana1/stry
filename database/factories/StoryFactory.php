<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class StoryFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(3, true);
        
        return [
            'author_id' => User::factory(), // Creates a new author automatically
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(100, 999),
            'title' => $title,
            'synopsis' => fake()->paragraphs(3, true),
            'genre' => fake()->randomElement(['LitRPG', 'Fantasy', 'Sci-Fi', 'Romance', 'Mystery', 'Horror']),
            'tags' => fake()->randomElements(['Adventure', 'Magic', 'Space', 'Dystopian', 'System']),
            'cover_image' => fake()->imageUrl(400, 600, 'book', true, 'Faker'),
            'cover_alt_text' => fake()->sentence(),
            'chapters_count' => 0, // Will be updated when chapters are created
            'followers_count' => fake()->numberBetween(0, 500),
            'status' => fake()->randomElement(['ongoing', 'completed', 'hiatus']),
            'last_chapter_published_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }

    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ongoing',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}