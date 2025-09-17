<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public static function generate_code()
    {
        $data = Invoice::where(['active' => 1])
            ->orderBy('created_at', 'desc')
            ->first();
        $last_number = 1;
        if (isset($data)) {
            $numbers = explode("-", $data->invoice_number);
            $last_number = intval($numbers[1]);
            $last_number++;
        }
        $last_code = 'INV-' . str_pad($last_number, 8, '0', STR_PAD_LEFT);
        return $last_code;
    }

    public static function get_all($request, $paid = false)
    {
        $query = Invoice::query();

        if ($paid == true) {
            $query->whereIn('invoice_status', [
                Constant::INV_STATUS_PAID,
            ]);
        }

        // Filter berdasarkan allowed branches jika ada
        if (!empty($request->allowed_branches)) {
            $query->whereIn('branch_id', $request->allowed_branches);
        }

        // Filter berdasarkan tahun dan bulan
        if (isset($request->year)) {
            $query->whereYear('due_date', $request->year);
        }
        if (isset($request->month)) {
            $query->whereMonth('due_date', $request->month);
        }

        $query = $query->where(['active' => 1])
            ->orderBy('invoice_number', 'asc');
        if (isset($request->branch)) {
            $query = $query->where(['branch_id' => $request->branch]);
        }

        $data = $query->get();

        $cash = $midtrans = $total = 0;
        foreach ($data as $k => $v) {
            if ($v->payment_method == Constant::PAYMENT_MIDTRANS) {
                $midtrans += $v->amount;
                $total += $v->amount;
            } else if ($v->payment_method == Constant::PAYMENT_CASH) {
                $cash += $v->amount;
                $total += $v->amount;
            }
        }
        return [
            'data' => $data,
            'cash' => $cash,
            'midtrans' => $midtrans,
            'total' => $total
        ];
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function branches(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
