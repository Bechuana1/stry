<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'chapter_id',
        'parent_id',
        'content',
        'ip_address',
        'user_agent',
        'report_count',
        'is_hidden',
        'is_edited',
    ];

    protected $casts = [
        'report_count' => 'integer',
        'is_hidden' => 'boolean',
        'is_edited' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // ---------- Relationships ----------
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // ---------- Scopes ----------
    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    // ---------- Helper Methods ----------
    public function report(): void
    {
        $this->increment('report_count');
        if ($this->report_count >= 3) {
            $this->update(['is_hidden' => true]);
        }
    }
}