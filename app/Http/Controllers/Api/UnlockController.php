<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\UserChapterUnlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnlockController extends Controller
{
    public function unlock(Chapter $chapter, Request $request)
    {
        $user = $request->user();

        // 1. Validate: Is the chapter already unlocked?
        if ($chapter->isUnlockedForUser($user)) {
            return response()->json([
                'message' => 'Chapter is already unlocked.',
                'already_unlocked' => true,
            ]);
        }

        // 2. Validate: Is the chapter free? (Shouldn't happen, but just in case)
        if ($chapter->is_free) {
            return response()->json([
                'message' => 'This chapter is free. No need to unlock.',
                'already_unlocked' => true,
            ]);
        }

        // 3. Check if user has enough Gems
        if ($user->gem_balance < 2) {
            return response()->json([
                'message' => 'Insufficient Gems. You need 2 Gems to unlock this chapter.',
                'gem_balance' => $user->gem_balance,
                'needed' => 2,
            ], 402); // Payment Required
        }

        // 4. Deduct gems and record the unlock (wrap in transaction)
        DB::transaction(function () use ($user, $chapter, $request) { // <-- FIX: Added $request to use clause
            // Deduct Gems using the User model method
            $user->deductGems(2);

            // Record the unlock
            UserChapterUnlock::create([
                'user_id' => $user->id,
                'chapter_id' => $chapter->id,
                'gems_spent' => 2,
                'ip_address' => $request->ip(),
            ]);

            // Increment the gem revenue on the chapter (for author stats)
            $chapter->increment('gem_revenue_earned', 2);
        });

        // Refresh the user model to get the updated gem_balance
        $user->refresh(); // <-- FIX: Using refresh() instead of fresh() for better compatibility

        return response()->json([
            'message' => 'Chapter unlocked successfully!',
            'gem_balance' => $user->gem_balance,
            'chapter_id' => $chapter->id,
        ]);
    }
}