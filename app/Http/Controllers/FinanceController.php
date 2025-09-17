<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\Master;
use App\Models\MasterProduct;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    //
    public function index(Request $request)
    {
        $start = $request->start ?? date("m/d/Y");
        $end = $request->end ?? date("m/d/Y");

        $inventory  = InventoryStock::where(['active' => 1])->get();
        $order  = Order::where(['active' => 1])
            ->whereRaw("created_at between '" . date("Y-m-d 00:00:00", strtotime($start)) . "' AND '" . date("Y-m-d 23:59:59", strtotime($end)) . "'")
            ->where('status', '=', null)
            ->get();

        $product = MasterProduct::where(['active' => 1])->get();
        $inventory_price = [];
        $master_product = [];

        foreach ($inventory as $k => $v) {
            $inventory_price[$v->product_id][$v->channel] = $v->price;
        }

        foreach ($product as $k => $v) {
            $stocks =  isset($v->stocks) ? json_decode($v->stocks, TRUE) : [];
            $mp_prices =  isset($v->mp_prices) ? json_decode($v->mp_prices, TRUE) : [];
            $master_product[$v->id]["stocks"] = $stocks;
            $master_product[$v->id]["mp_prices"] = $mp_prices;
        }

        $total_modal = 0;
        $total_penjualan = 0;
        $total_profit = 0;
        $admin_fee = 0;
        $sisa_modal = 0;

        foreach ($inventory as $k => $v) {
            $total_modal += $v->qty * $v->price;
        }

        $product_ordered = [];
        foreach ($order as $k => $v) {
            $total_penjualan  += $v->qty * $v->price;
            $sold = $inventory_price[$v->product_id][$v->channel] ?? 0;
            $total_profit += $v->qty * ($v->price - $sold);
            $admin_fee +=  $v->admin_fee;
            if (!isset($product_ordered[$v->product_id][$v->channel])) $product_ordered[$v->product_id][$v->channel] = 0;
            $product_ordered[$v->product_id][$v->channel] += $v->qty;
        }

        $product_inventory = [];

        foreach ($inventory as $k => $v) {
            if (!isset($product_inventory[$v->product_id][$v->channel])) $product_inventory[$v->product_id][$v->channel] = [];
            if (!isset($product_inventory[$v->product_id][$v->channel]["qty"])) $product_inventory[$v->product_id][$v->channel]["qty"] = 0;
            $product_inventory[$v->product_id][$v->channel]["qty"] += $v->qty;
            $product_inventory[$v->product_id][$v->channel]["price"] = $v->price;
        }

        $total_profit = $total_profit - $admin_fee;

        foreach ($product_inventory as $k => $v) {
            foreach ($v as $x => $y) {
                $usage = isset($product_ordered[$k]) && isset($product_ordered[$k][$x]) ? $product_ordered[$k][$x] : 0;
                $sisa_modal += ($y["qty"] - $usage) * $y["price"];
            }
        }

        return view('pages.finance.view', compact(
            'start',
            'end',
            'total_modal',
            'total_penjualan',
            'total_profit',
            'admin_fee',
            'sisa_modal'
        ));
    }
}
