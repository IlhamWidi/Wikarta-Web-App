<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Giveaway extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'giveaway_code',
        'name',
        'description',
        'type',
        'value',
        'percentage',
        'quantity',
        'claimed',
        'valid_from',
        'valid_until',
        'is_active',
        'terms_conditions',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'percentage' => 'integer',
        'quantity' => 'integer',
        'claimed' => 'integer',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'terms_conditions' => 'array',
    ];

    // Relationships
    public function claims()
    {
        return $this->hasMany(GiveawayClaim::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->whereDate('valid_from', '<=', now())
                     ->whereDate('valid_until', '>=', now());
    }

    public function scopeAvailable($query)
    {
        return $query->active()
                     ->whereColumn('claimed', '<', 'quantity');
    }

    // Accessors
    public function getIsValidAttribute()
    {
        return $this->is_active && 
               $this->valid_from <= now() && 
               $this->valid_until >= now();
    }

    public function getIsAvailableAttribute()
    {
        return $this->is_valid && $this->claimed < $this->quantity;
    }

    public function getRemainingQuantityAttribute()
    {
        return max(0, $this->quantity - $this->claimed);
    }

    // Auto-generate giveaway code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($giveaway) {
            if (empty($giveaway->giveaway_code)) {
                $giveaway->giveaway_code = 'GIVE-' . strtoupper(substr(uniqid(), -8));
            }
        });
    }
}
