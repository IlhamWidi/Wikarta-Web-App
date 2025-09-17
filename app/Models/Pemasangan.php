<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasangan extends Model
{
    protected $table = 'pemasangans';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'pelanggan_id',
        'panjang_kabel',
        'odp',
        'opm',
        'kode_ppoe',
        'user_ppoe',
        'password_ppoe',
        'vlan_ppoe',
        'foto_rumah',
        'teknisi_id',
        'odc',
        'keterangan',
        'status'
    ];

    // Relasi ke pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    // Relasi ke teknisi
    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }
}
