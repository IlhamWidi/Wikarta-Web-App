<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class GiveAway extends Model
{
    use HasUuids;

    protected $fillable = [
        'pelanggan_id',
        'tanggal',
        'give_away',
        'operator_id',
        'recipient_photo',
        'kurir_id',
        'status',
        'active'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'active' => 'boolean'
    ];

    /**
     * Relasi ke tabel users untuk data pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(User::class, 'pelanggan_id');
    }

    /**
     * Relasi ke tabel users untuk data operator
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * Relasi ke tabel users untuk data kurir
     */
    public function kurir()
    {
        return $this->belongsTo(User::class, 'kurir_id');
    }
}
