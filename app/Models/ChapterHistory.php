<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'revision',
        'content_markdown',
        'content_html',
        'word_count',
        'created_by',
        'rollback_comment',
    ];

    protected $casts = [
        'word_count' => 'integer',
        'revision' => 'integer',
    ];

    // ---------- Relationships ----------
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}