<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'story_id',
        'chapter_number',
        'slug',
        'title',
        'word_count',
        'estimated_read_seconds',
        'revision',
        'is_latest_revision',
        'status',
        'published_at',
        'scheduled_for',
        'locked_until',
        'gem_revenue_earned',
    ];

    protected $casts = [
        'is_latest_revision' => 'boolean',
        'published_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'locked_until' => 'datetime',
        'word_count' => 'integer',
        'estimated_read_seconds' => 'integer',
        'gem_revenue_earned' => 'integer',
        'deleted_at' => 'datetime',
    ];

    // ---------- Relationships ----------
    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function content()
    {
        return $this->hasOne(ChapterContent::class);
    }

    public function histories()
    {
        return $this->hasMany(ChapterHistory::class);
    }

    public function usersUnlocked()
    {
        return $this->belongsToMany(User::class, 'user_chapter_unlocks')
            ->withPivot('gems_spent', 'created_at')
            ->withTimestamps();
    }

    public function stats()
    {
        return $this->hasOne(ChapterStat::class);
    }

    public function viewEvents()
    {
        return $this->hasMany(ViewEvent::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function characters()
    {
        return $this->belongsToMany(Character::class, 'chapter_character');
    }

    // ---------- Scopes ----------
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_for', '<=', now());
    }

    public function scopeFree($query)
    {
        return $query->where('locked_until', '<=', now());
    }

    public function scopeLocked($query)
    {
        return $query->where('locked_until', '>', now());
    }

    // ---------- Accessors ----------
    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected function isFree(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->locked_until === null || $this->locked_until->isPast()
        );
    }

    // ---------- CRITICAL: Unlock Logic ----------
    /**
     * Check if this chapter is unlocked for a given user.
     * Rules:
     * 1. If user is Premium (active subscription) -> Unlocked.
     * 2. If chapter is free (locked_until is in the past) -> Unlocked.
     * 3. If user has spent Gems to unlock this specific chapter -> Unlocked.
     * 4. Otherwise -> Locked.
     */
    public function isUnlockedForUser(?User $user): bool
    {
        // If no user is logged in, only free chapters are accessible
        if (!$user) {
            return $this->is_free;
        }

        // Rule 1: Premium users get everything
        if ($user->is_active_premium) {
            return true;
        }

        // Rule 2: Chapter is free (3-day paywall has passed)
        if ($this->is_free) {
            return true;
        }

        // Rule 3: User specifically unlocked it with Gems
        return $user->hasUnlockedChapter($this);
    }

    /**
     * Get the content HTML for this chapter.
     * Automatically loads from the related ChapterContent model.
     */
    public function getHtmlContent(): ?string
    {
        return $this->content?->content_html;
    }

    public function getMarkdownContent(): ?string
    {
        return $this->content?->content_markdown;
    }

    // ---------- Helper Methods ----------
    public function markAsPublished(): void
    {
        $this->status = 'published';
        $this->published_at = now();
        $this->locked_until = now()->addDays(3); // 3-day paywall
        $this->save();

        // Increment story chapter count
        $this->story->incrementChaptersCount();
    }

    public function createRevision(string $markdown, string $html, int $wordCount, int $userId): void
    {
        // Save current state to history
        $this->histories()->create([
            'chapter_id' => $this->id,
            'revision' => $this->revision,
            'content_markdown' => $this->content->content_markdown,
            'content_html' => $this->content->content_html,
            'word_count' => $this->word_count,
            'created_by' => $userId,
            'rollback_comment' => null,
        ]);

        // Update content
        $this->content()->updateOrCreate(
            ['chapter_id' => $this->id],
            [
                'content_markdown' => $markdown,
                'content_html' => $html,
            ]
        );

        $this->word_count = $wordCount;
        $this->revision += 1;
        $this->is_latest_revision = true;
        $this->save();
    }

    public function rollbackToRevision(int $revision, int $userId): void
    {
        $history = $this->histories()->where('revision', $revision)->firstOrFail();

        // Save current as new revision before rolling back
        $this->createRevision(
            $history->content_markdown,
            $history->content_html,
            $history->word_count,
            $userId
        );

        // Mark that this was a rollback
        $latest = $this->histories()->latest()->first();
        $latest->update(['rollback_comment' => "Rolled back to revision #{$revision}"]);
    }
}