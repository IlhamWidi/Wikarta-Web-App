<?php

namespace App\Http\Controllers;

use App\Models\Master;
use App\Models\MasterProduct;
use App\Models\TransferStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferStockController extends Controller
{
    //
    public function index(Request $request)
    {
        $loginner = Auth::user();
        $data = TransferStock::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        $ms_product = MasterProduct::where(['active' => 1])->get();
        $ms_channel = Master::MarketPlace();

        return view('pages.transfer-stock.view', compact('data', 'ms_product', 'ms_channel'));
    }

    public function store(Request $request)
    {
        $rules = [
            'product_id' => 'required',
            'source_channel' => 'required',
            'destination_channel' => 'required',
            'qty' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("transfer-stock")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                $product = MasterProduct::where(['id' => $request->product_id])->first();
                $stock =  isset($product->stocks) ? json_decode($product->stocks, TRUE) : [];
                $stock[$request->source_channel] = isset($stock[$request->source_channel]) ? $stock[$request->source_channel] : 0;
                $stock[$request->destination_channel] = isset($stock[$request->destination_channel]) ? $stock[$request->destination_channel] : 0;

                if ($stock[$request->source_channel] < $request->qty) {
                    return redirect()->route('transfer-stock')
                        ->withInput($request->all())
                        ->with('message_error', 'Error exception!! stock tidak cukup');
                }

                $loginner = Auth::user();

                DB::beginTransaction();
                $data = new TransferStock();
                $data->id = Str::uuid();
                $data->user_id = $loginner->id;
                $data->product_id = $request->product_id;
                $data->source_channel = $request->source_channel;
                $data->destination_channel = $request->destination_channel;
                $data->qty = $request->qty;
                $data->save();

                /// update to product master stock
                $stock[$request->source_channel] -= $request->qty;
                $stock[$request->destination_channel] += $request->qty;
                $product->stocks = json_encode($stock);
                $product->save();

                DB::commit();
                return redirect()->route('transfer-stock')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('transfer-stock')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $data = TransferStock::where([
            'active' => 1,
        ])
            ->get();
        $one = TransferStock::where(['id' => $id])->first();
        return view('pages.transfer-stock.view', compact('data', 'one'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'product_id' => 'required',
            'source_channel' => 'required',
            'destination_channel' => 'required',
            'qty' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("transfer-stock")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = TransferStock::where(['id' => $id])->first();
                $data->name = $request->name;
                $data->save();
                DB::commit();
                return redirect()->route('order')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('order')
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
            return redirect()->route('order')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {

            DB::beginTransaction();

            $data = TransferStock::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            /// update to product master stock
            $product = MasterProduct::where(['id' => $data->product_id])->first();
            $stock =  isset($product->stocks) ? json_decode($product->stocks, TRUE) : [];
            $stock[$data->source_channel] = isset($stock[$data->source_channel]) ? $stock[$data->source_channel] : 0;
            $stock[$data->destination_channel] = isset($stock[$data->destination_channel]) ? $stock[$data->destination_channel] : 0;
            $stock[$data->source_channel] += $data->qty;
            $stock[$data->destination_channel] -= $data->qty;
            $product->stocks = json_encode($stock);
            $product->save();

            DB::commit();

            return redirect()->route('order')
                ->with('message_success', 'Delete data success.');
        }
    }
}
