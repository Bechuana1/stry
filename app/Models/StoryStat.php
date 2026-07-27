<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryStat extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'story_id',
        'total_views',
        'updated_at',
    ];

    protected $casts = [
        'total_views' => 'integer',
        'updated_at' => 'datetime',
    ];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function incrementViews(int $amount = 1): void
    {
        $this->increment('total_views', $amount);
        $this->update(['updated_at' => now()]);
    }
}