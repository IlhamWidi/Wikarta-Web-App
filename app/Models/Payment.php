<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_code',
        'invoice_id',
        'customer_id',
        'order_id',
        'transaction_id',
        'payment_method',
        'payment_type',
        'amount',
        'status',
        'va_number',
        'bill_key',
        'biller_code',
        'payment_url',
        'qr_code_url',
        'settlement_time',
        'expiry_time',
        'metadata',
        'notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'settlement_time' => 'datetime',
        'expiry_time' => 'datetime',
        'metadata' => 'array',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSettled($query)
    {
        return $query->whereIn('status', ['settlement', 'capture']);
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['deny', 'cancel', 'expire']);
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getIsSettledAttribute()
    {
        return in_array($this->status, ['settlement', 'capture']);
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_time && $this->expiry_time < now();
    }

    // Auto-generate payment code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_code)) {
                $payment->payment_code = 'PAY-' . date('Ymd') . '-' . strtoupper(uniqid());
            }

            if (empty($payment->order_id)) {
                $payment->order_id = self::generateOrderId();
            }
        });
    }

    public static function generateOrderId(): string
    {
        do {
            $orderId = 'PAY-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));
        } while (self::where('order_id', $orderId)->exists());

        return $orderId;
    }
}
