<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'date',
        'in_time',
        'in_lat',
        'in_lng',
        'out_time',
        'out_lat',
        'out_lng'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
