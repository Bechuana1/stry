<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'story_id',
        'name',
        'description',
        'avatar',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // ---------- Relationships ----------
    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function chapters()
    {
        return $this->belongsToMany(Chapter::class, 'chapter_character');
    }

    public function fanArt()
    {
        return $this->hasMany(FanArt::class);
    }
}