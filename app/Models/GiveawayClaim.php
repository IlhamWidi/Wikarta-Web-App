<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiveawayClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'giveaway_id',
        'customer_id',
        'claimed_at',
        'used_at',
        'invoice_id',
        'status',
        'notes',
        'approved_by',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    // Relationships
    public function giveaway()
    {
        return $this->belongsTo(Giveaway::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeClaimed($query)
    {
        return $query->where('status', 'claimed');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }
}
