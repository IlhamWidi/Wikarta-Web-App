<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'installation_fee',
        'speed_mbps',
        'quota_gb',
        'period',
        'period_months',
        'is_active',
        'is_featured',
        'features',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'installation_fee' => 'decimal:2',
        'speed_mbps' => 'integer',
        'quota_gb' => 'integer',
        'period_months' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'features' => 'array',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function installations()
    {
        return $this->hasMany(Installation::class);
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
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedSpeedAttribute()
    {
        return $this->speed_mbps . ' Mbps';
    }

    public function getQuotaTextAttribute()
    {
        return $this->quota_gb ? $this->quota_gb . ' GB' : 'Unlimited';
    }

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = \Illuminate\Support\Str::slug($package->name);
            }
        });

        static::updating(function ($package) {
            if ($package->isDirty('name') && empty($package->slug)) {
                $package->slug = \Illuminate\Support\Str::slug($package->name);
            }
        });
    }
}
