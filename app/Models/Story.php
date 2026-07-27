<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_id',
        'slug',
        'title',
        'synopsis',
        'genre',
        'tags',
        'cover_image',
        'cover_alt_text',
        'chapters_count',
        'followers_count',
        'status',
        'last_chapter_published_at',
    ];

    protected $casts = [
        'tags' => 'json',
        'chapters_count' => 'integer',
        'followers_count' => 'integer',
        'last_chapter_published_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ---------- Relationships ----------
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function publishedChapters()
    {
        return $this->hasMany(Chapter::class)->where('status', 'published');
    }

    public function characters()
    {
        return $this->hasMany(Character::class);
    }

    public function stats()
    {
        return $this->hasOne(StoryStat::class);
    }

    public function fanArt()
    {
        return $this->hasMany(FanArt::class);
    }

    // ---------- Scopes ----------
    public function scopePublished($query)
    {
        return $query->where('status', 'ongoing')->orWhere('status', 'completed');
    }

    public function scopeByGenre($query, $genre)
    {
        return $query->where('genre', $genre);
    }

    // ---------- Accessors ----------
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getUrlAttribute(): string
    {
        return route('story.show', $this->slug);
    }

    // ---------- Helper Methods ----------
    public function getNextChapterNumber(): int
    {
        return $this->chapters()->max('chapter_number') + 1;
    }

    public function incrementChaptersCount(): void
    {
        $this->increment('chapters_count');
        $this->update(['last_chapter_published_at' => now()]);
    }
}