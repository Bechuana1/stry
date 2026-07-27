<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\ViewEvent;
use Illuminate\Http\Request;


class ChapterController extends Controller
{
    /**
     * Display a chapter.
     * Returns the full HTML content if unlocked, otherwise returns a "locked" status.
     */
    public function show(Chapter $chapter, Request $request)
    {
        // $user = auth()->user();
        $user = $request->user(); // Use the request's user for API token auth

        // Check if the chapter is published
        if ($chapter->status !== 'published') {
            return response()->json(['message' => 'Chapter not available.'], 404);
        }

        // Check unlock status using our model method
        $isUnlocked = $chapter->isUnlockedForUser($user);

        // Prepare base response
        $response = [
            'id' => $chapter->id,
            'chapter_number' => $chapter->chapter_number,
            'title' => $chapter->title,
            'story_id' => $chapter->story_id,
            'story_slug' => $chapter->story->slug,
            'published_at' => $chapter->published_at,
            'word_count' => $chapter->word_count,
            'estimated_read_seconds' => $chapter->estimated_read_seconds,
            'is_unlocked' => $isUnlocked,
            'is_free' => $chapter->is_free,
            'locked_until' => $chapter->locked_until,
            'previous_chapter_id' => null,
            'next_chapter_id' => null,
        ];

        // If unlocked, attach the content and navigation
        if ($isUnlocked) {
            $response['content_html'] = $chapter->getHtmlContent();

            // Record the view event (async friendly)
            ViewEvent::create([
                'chapter_id' => $chapter->id,
                'user_id' => $user?->id,
                'session_id' => $request->session()->getId(),
                'ip_address' => $request->ip(),
            ]);

            // Get adjacent chapters for navigation
            $response['previous_chapter_id'] = Chapter::where('story_id', $chapter->story_id)
                ->where('chapter_number', '<', $chapter->chapter_number)
                ->where('status', 'published')
                ->orderBy('chapter_number', 'desc')
                ->value('id');

            $response['next_chapter_id'] = Chapter::where('story_id', $chapter->story_id)
                ->where('chapter_number', '>', $chapter->chapter_number)
                ->where('status', 'published')
                ->orderBy('chapter_number', 'asc')
                ->value('id');

            // Update user reading progress (streak & last read)
            if ($user) {
                $user->updateReadingStreak();
                $user->update(['last_read_chapter_id' => $chapter->id]);
            }
        }

        return response()->json($response);
    }
}