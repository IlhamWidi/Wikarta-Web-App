<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public static function generate_code()
    {
        $data = Branch::where(['active' => 1])
            ->orderBy('created_at', 'desc')
            ->first();
        $last_number = 1;
        if (isset($data)) {
            $numbers = explode("-", $data->code);
            $last_number = intval($numbers[1]);
            $last_number++;
        }
        $last_code = 'BRC-' . str_pad($last_number, 8, '0', STR_PAD_LEFT);
        return $last_code;
    }
}
