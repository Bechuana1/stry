<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'type',
        'amount_cents',
        'gem_amount',
        'subscription_interval',
        'status',
        'failure_reason',
        'refunded_at',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'gem_amount' => 'integer',
        'refunded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ---------- Relationships ----------
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ---------- Accessors ----------
    public function getAmountUsdAttribute(): float
    {
        return $this->amount_cents / 100;
    }

    // ---------- Scopes ----------
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    // ---------- Helper Methods ----------
    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->save();
    }

    public function markAsRefunded(): void
    {
        $this->status = 'refunded';
        $this->refunded_at = now();
        $this->save();
    }
}