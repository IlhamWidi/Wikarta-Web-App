<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'customer_id',
        'branch_id',
        'package_id',
        'address',
        'kode_server',
        'password_server',
        'vlan',
        'odp',
        'odc',
        'keterangan',
        'keluhan',
        'feedback',
        'teknisi_id',
        'status',
        'photo'
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }
}
