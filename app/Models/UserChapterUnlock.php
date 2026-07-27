<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChapterUnlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chapter_id',
        'gems_spent',
        'ip_address',
    ];

    protected $casts = [
        'gems_spent' => 'integer',
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
}