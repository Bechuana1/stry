<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'settings',
        'gem_balance',
        'is_premium',
        'premium_expires_at',
        'reading_streak',
        'last_streak_update',
        'total_read_seconds',
        'last_login_ip',
        'last_login_at',
        'is_banned',
        'banned_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'premium_expires_at' => 'datetime',
        'last_streak_update' => 'date',
        'last_login_at' => 'datetime',
        'settings' => 'json',
        'is_premium' => 'boolean',
        'is_banned' => 'boolean',
        'gem_balance' => 'integer',
        'reading_streak' => 'integer',
        'total_read_seconds' => 'integer',
    ];

    // ---------- Relationships ----------
    public function stories()
    {
        return $this->hasMany(Story::class, 'author_id');
    }

    public function chapters()
    {
        return $this->belongsToMany(Chapter::class, 'user_chapter_unlocks')
            ->withPivot('gems_spent', 'created_at')
            ->withTimestamps();
    }

    public function unlockedChapters()
    {
        return $this->belongsToMany(Chapter::class, 'user_chapter_unlocks');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function follows()
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function followedAuthors()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_user_id')
            ->withPivot('notify_by_email')
            ->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_user_id', 'follower_id')
            ->withPivot('notify_by_email')
            ->withTimestamps();
    }

    public function viewEvents()
    {
        return $this->hasMany(ViewEvent::class);
    }

    public function loginTokens()
    {
        return $this->hasMany(LoginToken::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function fanArt()
    {
        return $this->hasMany(FanArt::class, 'uploader_id');
    }

    // ---------- Accessors & Mutators ----------
    protected function isActivePremium(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_premium && ($this->premium_expires_at === null || $this->premium_expires_at->isFuture())
        );
    }

    // ---------- Helper Methods ----------
    public function hasUnlockedChapter(Chapter $chapter): bool
    {
        return $this->unlockedChapters()->where('chapter_id', $chapter->id)->exists();
    }

    public function addGems(int $amount): self
    {
        $this->increment('gem_balance', $amount);
        return $this;
    }

    public function deductGems(int $amount): bool
    {
        if ($this->gem_balance < $amount) {
            return false;
        }
        $this->decrement('gem_balance', $amount);
        return true;
    }

    public function updateReadingStreak(): void
    {
        $today = now()->toDateString();
        if ($this->last_streak_update && $this->last_streak_update->toDateString() === $today) {
            return; // Already updated today
        }

        if ($this->last_streak_update && $this->last_streak_update->diffInDays(now()) === 1) {
            $this->increment('reading_streak');
        } else {
            $this->reading_streak = 1;
        }

        $this->last_streak_update = now();
        $this->save();
    }
}