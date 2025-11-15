<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'event_key',
        'order_id',
        'transaction_id',
        'transaction_status',
        'payment_type',
        'fraud_status',
        'gross_amount',
        'transaction_time',
        'settlement_time',
        'payload',
        'signature_key',
        'is_verified',
        'is_processed',
        'processed_at',
        'processing_error',
        'retry_count',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'transaction_time' => 'datetime',
        'settlement_time' => 'datetime',
        'payload' => 'array',
        'is_verified' => 'boolean',
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
        'retry_count' => 'integer',
    ];

    // Scopes
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeNeedRetry($query)
    {
        return $query->where('is_processed', false)
                     ->where('retry_count', '<', 3)
                     ->where('created_at', '>', now()->subHours(24));
    }

    // Methods
    public function markAsProcessed()
    {
        $this->update([
            'is_processed' => true,
            'processed_at' => now(),
        ]);
    }

    public function incrementRetry()
    {
        $this->increment('retry_count');
    }
}
