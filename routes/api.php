<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChapterController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\UnlockController;
use Illuminate\Support\Facades\Route;

// ---------- Public Routes (No Auth required) ----------
Route::post('/auth/request-link', [AuthController::class, 'requestLink']);
Route::get('/auth/verify', [AuthController::class, 'verify'])->name('auth.verify');

// Stories (Public reading)
Route::get('/stories', [StoryController::class, 'index']);
Route::get('/stories/genres', [StoryController::class, 'genres']);
Route::get('/stories/{story:slug}', [StoryController::class, 'show']);

// Chapters (Public viewing, but locked content is filtered)
Route::get('/chapters/{chapter:slug}', [ChapterController::class, 'show']);

// ---------- Authenticated Routes (Requires Sanctum/Session) ----------
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Unlock (Spend Gems)
    Route::post('/chapters/{chapter}/unlock', [UnlockController::class, 'unlock']);

    // Add later: Library, Follows, Comments, etc.
});