<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleAccessMenu extends Model
{
    protected $fillable = ['user_id', 'menu_access'];
    protected $casts = [
        'menu_access' => 'array',
    ];
}
