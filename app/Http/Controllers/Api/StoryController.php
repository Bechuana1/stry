<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    /**
     * List stories (Homepage feed).
     * Supports filtering by genre, sorting, and full-text search.
     */
    public function index(Request $request)
    {
        $query = Story::with('author:id,name,avatar')
            ->withCount('chapters') // For total chapters (though we have denormalized)
            ->where('status', '!=', 'hiatus');

        // Search
        if ($request->filled('search')) {
            $query->whereFullText(['title', 'synopsis'], $request->search);
        }

        // Genre filter
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('followers_count', 'desc');
                break;
            case 'trending':
                $query->orderBy('total_views', 'desc'); // We'll populate this from stats later
                break;
            case 'completed':
                $query->where('status', 'completed')->orderBy('last_chapter_published_at', 'desc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $stories = $query->paginate(20);

        return response()->json($stories);
    }

    /**
     * Show a single story with its Table of Contents.
     */
    public function show(Story $story)
    {
        // Eager load only the metadata needed for TOC (no blobs!)
        $chapters = $story->chapters()
            ->where('status', 'published')
            ->orderBy('chapter_number')
            ->get(['id', 'chapter_number', 'slug', 'title', 'published_at', 'locked_until']);

        // Attach unlock status for the authenticated user
        // $user = auth()->user();
        $user = request()->user(); // Use the request's user for API token auth
        $chapters->each(function ($chapter) use ($user) {
            $chapter->is_unlocked = $chapter->isUnlockedForUser($user);
        });

        $story->load('author:id,name,avatar');
        $story->setRelation('chapters', $chapters);

        return response()->json($story);
    }

    /**
     * Get distinct genres for the filter dropdown.
     */
    public function genres()
    {
        $genres = Story::distinct()->pluck('genre');
        return response()->json($genres);
    }
}