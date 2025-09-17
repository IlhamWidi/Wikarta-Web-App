<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Constant extends Model
{
    use HasFactory;

    public const USER_TYPE_ADMIN = "ADMIN";
    public const USER_TYPE_CUSTOMER = "CUSTOMER";

    public const INV_STATUS_PAID = "SETTLEMENT";
    public const INV_STATUS_UNPAID = "UNPAID";

    public const PAYMENT_MIDTRANS = "MIDTRANS";
    public const PAYMENT_CASH = "CASH";

    public const ORDER_RETUR = "RETUR";

    public const STATUS_RETUR_RUSAK = "RUSAK";
    public const STATUS_RETUR_NORMAL = "NORMAL";
}
