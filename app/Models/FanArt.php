<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FanArt extends Model
{
    use HasFactory;

    protected $fillable = [
        'uploader_id',
        'character_id',
        'story_id',
        'image_url',
        'image_public_id',
        'title',
        'moderation_status',
        'moderated_by',
        'moderated_at',
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
    ];

    // ---------- Relationships ----------
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    // ---------- Scopes ----------
    public function scopeApproved($query)
    {
        return $query->where('moderation_status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('moderation_status', 'pending');
    }

    // ---------- Helper Methods ----------
    public function approve(int $moderatorId): void
    {
        $this->moderation_status = 'approved';
        $this->moderated_by = $moderatorId;
        $this->moderated_at = now();
        $this->save();
    }

    public function reject(int $moderatorId): void
    {
        $this->moderation_status = 'rejected';
        $this->moderated_by = $moderatorId;
        $this->moderated_at = now();
        $this->save();
    }
}