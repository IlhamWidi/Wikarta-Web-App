<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Installation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'installation_number',
        'customer_id',
        'package_id',
        'scheduled_date',
        'scheduled_time',
        'completed_date',
        'completed_time',
        'status',
        'installation_address',
        'latitude',
        'longitude',
        'technician_id',
        'equipment_used',
        'notes',
        'photos',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'photos' => 'array',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
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
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereDate('scheduled_date', '>=', today())
                     ->whereDate('scheduled_date', '<=', today()->addDays($days));
    }

    public function scopeForTechnician($query, $technicianId)
    {
        return $query->where('technician_id', $technicianId);
    }

    // Auto-generate installation number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($installation) {
            if (empty($installation->installation_number)) {
                $installation->installation_number = 'INST-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            }
        });
    }
}
