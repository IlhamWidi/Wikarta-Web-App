<?php

namespace App\Http\Controllers;

use App\Models\Constant;
use App\Models\Order;
use App\Models\Master;
use App\Models\MasterProduct;
use App\Models\Product;
use App\Models\Retur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReturController extends Controller
{
    //
    public function index(Request $request)
    {
        $loginner = Auth::user();

        $data = Retur::where([
            'active' => 1,
            'user_id' => $loginner->id
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        $ms_order = Order::where([
            'active' => 1,
            'user_id' => $loginner->id
        ])
            ->where('status', '=', null)
            ->get();

        $ms_retur_status = Master::ReturStatus();

        return view('pages.retur.view', compact('data', 'ms_order', 'ms_retur_status'));
    }

    public function store(Request $request)
    {
        $rules = [
            'order_id' => 'required',
            'status' => 'required',
            'notes' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("retur")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                $loginner = Auth::user();

                DB::beginTransaction();
                $data = new Retur();
                $data->id = Str::uuid();
                $data->user_id = $loginner->id;
                $data->order_id = $request->order_id;
                $data->status = $request->status;
                $data->notes = $request->notes;

                $order = Order::where(['id' => $request->order_id])->first();
                $order->status = Constant::ORDER_RETUR;

                /// update to product master stock
                if ($request->status == Constant::STATUS_RETUR_NORMAL) {
                    $product = MasterProduct::where(['id' =>  $order->product_id])->first();
                    $stock =  isset($product->stocks) ? json_decode($product->stocks, TRUE) : [];
                    $stock[$order->channel] += $order->qty;
                    $product->stocks = json_encode($stock);
                    $product->save();
                }

                $order->save();
                $data->save();
                DB::commit();
                return redirect()->route('retur')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('retur')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $data = Retur::where([
            'active' => 1,
        ])
            ->get();
        $one = Retur::where(['id' => $id])->first();
        return view('pages.retur.view', compact('data', 'one'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("retur")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = Retur::where(['id' => $id])->first();
                $data->name = $request->name;
                $data->save();
                DB::commit();
                return redirect()->route('retur')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('retur')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function delete(Request $request)
    {
        $rules = [
            "id" => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('retur')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            $data = Retur::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            $order = Order::where(['id' => $data->order_id])->first();
            $order->status = null;
            $order->save();

            /// update to product master stock
            if ($request->status == Constant::STATUS_RETUR_NORMAL) {
                $product = MasterProduct::where(['id' =>  $order->product_id])->first();
                $stock =  isset($product->stocks) ? json_decode($product->stocks, TRUE) : [];
                $stock[$order->channel] -= $order->qty;
                $product->stocks = json_encode($stock);
                $product->save();
            }

            return redirect()->route('retur')
                ->with('message_success', 'Delete data success.');
        }
    }
}
