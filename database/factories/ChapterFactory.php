<?php

namespace Database\Factories;

use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChapterFactory extends Factory
{
    public function definition(): array
    {
        // Generate a publish date between 30 days ago and now
        $publishedAt = fake()->dateTimeBetween('-30 days', 'now');
        
        // Determine if this chapter should be locked (published within the last 3 days)
        $lockedUntil = (clone $publishedAt)->modify('+3 days');
        
        // If it's older than 3 days, it's already free (locked_until is in the past)
        // If it's newer, it's still locked.
        $isOld = $publishedAt < now()->subDays(3);
        
        return [
            'story_id' => Story::factory(),
            'chapter_number' => 1, // We'll override this in the seeder
            'slug' => fake()->slug(2),
            'title' => fake()->sentence(4),
            'word_count' => fake()->numberBetween(1500, 5000),
            'estimated_read_seconds' => fake()->numberBetween(60, 300),
            'revision' => 1,
            'is_latest_revision' => true,
            'status' => 'published',
            'published_at' => $publishedAt,
            'scheduled_for' => null,
            'locked_until' => $isOld ? now()->subDays(1) : $lockedUntil, // Old=free, New=locked
            'gem_revenue_earned' => 0,
        ];
    }

    // State for a scheduled (unpublished) chapter
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'scheduled',
            'published_at' => null,
            'locked_until' => null,
        ]);
    }

    // State for a draft
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
            'locked_until' => null,
        ]);
    }
}