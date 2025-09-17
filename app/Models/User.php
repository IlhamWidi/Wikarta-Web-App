<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_SUPERUSER = 'SUPERUSER';
    const ROLE_OPERATOR = 'OPERATOR';
    const ROLE_MARKETING = 'MARKETING';
    const ROLE_TEKNISI = 'TEKNISI';

    // Enum values for subscribe_status
    const SUBSCRIBE_STATUS_INACTIVE = 'INACTIVE';
    const SUBSCRIBE_STATUS_ACTIVE = 'ACTIVE';
    const SUBSCRIBE_STATUS_ISOLIR = 'ISOLIR';
    const SUBSCRIBE_STATUS_DISMANTEL = 'DISMANTEL';

    public static function getSubscribeStatusOptions()
    {
        return [
            self::SUBSCRIBE_STATUS_ACTIVE => 'Active',
            self::SUBSCRIBE_STATUS_INACTIVE => 'Inactive',
            self::SUBSCRIBE_STATUS_ISOLIR => 'Isolir',
            self::SUBSCRIBE_STATUS_DISMANTEL => 'Dismantel',
        ];
    }
    public static function getRoles()
    {
        return [
            self::ROLE_SUPERUSER => 'Super User',
            self::ROLE_OPERATOR => 'Operator',
            self::ROLE_MARKETING => 'Marketing',
            self::ROLE_TEKNISI => 'Teknisi'
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'kode_server',
        'password_server',
        'vlan',
        'subscribe_status',
        'register_date',
        'activation_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'allowed_branches' => 'array'
    ];

    /**
     * Always encrypt password when it is updated.
     *
     * @param $value
     * @return string
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public static function generate_code()
    {
        $data = User::where(['user_type' => Constant::USER_TYPE_ADMIN])
            ->orderBy('created_at', 'desc')
            ->first();
        $last_number = 1;
        if (isset($data)) {
            $numbers = explode("-", $data->code);
            $last_number = intval($numbers[1]);
            $last_number++;
        }
        $last_code = 'ID-' . str_pad($last_number, 8, '0', STR_PAD_LEFT);
        return $last_code;
    }

    public function branches(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function packages(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }

    /**
     * Generate customer code with pattern YYmmddxxx (xxx = 3 digit increment, reset per day)
     */
    public static function generateCustomerCode()
    {
        $prefix = date('ymd');
        $last = self::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();
        $inc = 1;
        if ($last && preg_match('/^' . $prefix . '(\d{3})$/', $last->code, $m)) {
            $inc = intval($m[1]) + 1;
        }
        return $prefix . str_pad($inc, 3, '0', STR_PAD_LEFT);
    }
}
