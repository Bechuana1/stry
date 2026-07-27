<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterStat extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'chapter_id',
        'total_views',
        'unique_readers',
        'updated_at',
    ];

    protected $casts = [
        'total_views' => 'integer',
        'unique_readers' => 'integer',
        'updated_at' => 'datetime',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function incrementViews(int $amount = 1): void
    {
        $this->increment('total_views', $amount);
        $this->update(['updated_at' => now()]);
    }
}