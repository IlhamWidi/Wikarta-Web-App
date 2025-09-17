<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nama_pelanggan',
        'paket_id',
        'nomor_hp',
        'alamat_psb',
        'odp',
        'panjang_kabel',
        'foto_ktp',
        'status',
        'keterangan',
        'teknisi_id',
        'marketing_id',
        'active',
    ];

    // Relasi dengan paket
    public function paket()
    {
        return $this->belongsTo(Package::class, 'paket_id');
    }

    public function marketing()
    {
        return $this->belongsTo(User::class, 'marketing_id');
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }
}
