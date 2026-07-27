<?php

namespace Database\Factories;

use App\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChapterContentFactory extends Factory
{
    public function definition(): array
    {
        $markdown = fake()->paragraphs(15, true);
        
        // Convert simple newlines to HTML paragraphs (simulating rendering)
        $html = '<p>' . str_replace("\n", '</p><p>', $markdown) . '</p>';
        
        return [
            'chapter_id' => Chapter::factory(),
            'content_markdown' => $markdown,
            'content_html' => $html,
        ];
    }
}