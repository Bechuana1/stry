<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\ChapterContent;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Reaction;
use App\Models\Story;
use App\Models\User;
use App\Models\UserChapterUnlock;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. Create the "Main" Test User ---
        $mainUser = User::factory()->create([
            'name' => 'Test Reader',
            'email' => 'reader@stry.com',
            'gem_balance' => 50,
            'is_premium' => false,
            'reading_streak' => 7,
        ]);

        // --- 2. Create a Premium User ---
        $premiumUser = User::factory()->premium()->create([
            'name' => 'Premium Chad',
            'email' => 'premium@stry.com',
            'gem_balance' => 999,
        ]);

        // --- 3. Create 3 Authors ---
        $authors = User::factory()->count(3)->create([
            'gem_balance' => 200,
        ]);

        // --- 4. Create 10 Stories (across authors) ---
        $stories = collect();
        foreach ($authors as $author) {
            $storiesForAuthor = Story::factory()
                ->count(rand(3, 4))
                ->ongoing()
                ->create([
                    'author_id' => $author->id,
                    'followers_count' => rand(10, 500),
                ]);
            $stories = $stories->merge($storiesForAuthor);
        }

        // Add 2 completed stories
        $completedStories = Story::factory()->count(2)->completed()->create([
            'author_id' => $authors->first()->id,
            'chapters_count' => 20,
        ]);
        $stories = $stories->merge($completedStories);

        // --- 5. Create Chapters for each Story ---
        foreach ($stories as $story) {
            $chapterCount = rand(5, 15);
            for ($i = 1; $i <= $chapterCount; $i++) {
                $isOld = $i <= 3;
                $publishedAt = $isOld
                    ? now()->subDays(rand(4, 30))
                    : now()->subDays(rand(0, 2));
                $lockedUntil = $isOld
                    ? now()->subDays(rand(1, 5))
                    : now()->addDays(rand(1, 3));

                $chapter = Chapter::factory()->create([
                    'story_id' => $story->id,
                    'chapter_number' => $i,
                    'title' => "Chapter {$i}: " . fake()->sentence(3),
                    'published_at' => $publishedAt,
                    'locked_until' => $lockedUntil,
                    'status' => 'published',
                ]);

                ChapterContent::factory()->create([
                    'chapter_id' => $chapter->id,
                ]);

                if ($i % 2 === 0 && rand(1, 10) > 7) {
                    UserChapterUnlock::create([
                        'user_id' => $mainUser->id,
                        'chapter_id' => $chapter->id,
                        'gems_spent' => 2,
                        'ip_address' => '127.0.0.1',
                    ]);
                    $chapter->increment('gem_revenue_earned', 2);
                }
            }

            $story->update([
                'chapters_count' => $chapterCount,
                'last_chapter_published_at' => Chapter::where('story_id', $story->id)->max('published_at'),
            ]);
        }

        // --- 6. Follow a few authors (no duplicates) ---
        $authorsToFollow = $authors->random(min(3, $authors->count()));
        foreach ($authorsToFollow as $author) {
            Follow::firstOrCreate([
                'follower_id' => $mainUser->id,
                'followed_user_id' => $author->id,
            ], [
                'notify_by_email' => true,
            ]);
        }

        // --- 7. Seed Comments ---
        $publishedChapters = Chapter::where('status', 'published')->get();
        $createdComments = collect();

        foreach ($publishedChapters->random(min(10, $publishedChapters->count())) as $chapter) {
            $comment = Comment::create([
                'user_id' => $mainUser->id,
                'chapter_id' => $chapter->id,
                'parent_id' => null,
                'content' => fake()->sentence(10),
                'ip_address' => '127.0.0.1',
                'is_hidden' => false,
            ]);
            $createdComments->push($comment);

            if (rand(1, 10) > 7 && $createdComments->count() > 0) {
                $parentComment = $createdComments->random();
                Comment::create([
                    'user_id' => $premiumUser->id,
                    'chapter_id' => $chapter->id,
                    'parent_id' => $parentComment->id,
                    'content' => fake()->sentence(8),
                    'ip_address' => '127.0.0.1',
                    'is_hidden' => false,
                ]);
            }
        }

        // --- 8. Seed Reactions ---
        foreach ($publishedChapters->random(min(20, $publishedChapters->count())) as $chapter) {
            Reaction::create([
                'user_id' => $mainUser->id,
                'chapter_id' => $chapter->id,
                'reaction_type' => fake()->randomElement(['love', 'laugh', 'wow', 'insight']),
                'ip_address' => '127.0.0.1',
            ]);
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info("📧 Test User: reader@stry.com (Gem Balance: {$mainUser->gem_balance})");
        $this->command->info("📧 Premium User: premium@stry.com (Has Active Subscription)");
        $this->command->info("📚 Total Stories: " . Story::count());
        $this->command->info("📖 Total Chapters: " . Chapter::count());
    }
}