<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'content_markdown',
        'content_html',
    ];

    protected $casts = [
        'chapter_id' => 'integer',
    ];

    // ---------- Relationships ----------
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}