<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Master;
use App\Models\MasterProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Export\HtmlExport;

class OrderController extends Controller
{
    //
    public function index(Request $request)
    {
        $loginner = Auth::user();
        $data = Order::where([
            'active' => 1,
            'user_id' => $loginner->id
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        $ms_product = MasterProduct::where(['active' => 1])->get();
        $ms_channel = Master::MarketPlace();

        return view('pages.order.view', compact('data', 'ms_product', 'ms_channel'));
    }

    public function store(Request $request)
    {
        $rules = [
            'product_id' => 'required',
            'channel' => 'required',
            'qty' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("order")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                $product = MasterProduct::where(['id' => $request->product_id])->first();
                $mp_prices =  isset($product->mp_prices) ? json_decode($product->mp_prices, TRUE) : [];
                $admin_fees =  isset($product->admin_fees) ? json_decode($product->admin_fees, TRUE) : [];
                $stock =  isset($product->stocks) ? json_decode($product->stocks, TRUE) : [];
                $stock[$request->channel] = isset($stock[$request->channel]) ? $stock[$request->channel] : 0;

                if ($stock[$request->channel] < $request->qty) {
                    return redirect()->route('order')
                        ->withInput($request->all())
                        ->with('message_error', 'Error exception!! stock tidak cukup');
                }

                $loginner = Auth::user();

                DB::beginTransaction();
                $data = new Order();
                $data->id = Str::uuid();
                $data->user_id = $loginner->id;
                $data->product_id = $request->product_id;
                $data->channel = $request->channel;
                $data->qty = $request->qty;
                $data->price = $mp_prices[$request->channel] ?? 0;
                $persentage = $admin_fees[$request->channel] ?? 0;
                $data->admin_fee =  $persentage > 0 ? $persentage / 100 * $data->price * $request->qty : 0;
                $data->save();

                /// update to product master stock
                $stock[$request->channel] -= $request->qty;
                $product->stocks = json_encode($stock);
                $product->save();

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

    public function edit(Request $request, $id)
    {
        $data = Order::where([
            'active' => 1,
        ])
            ->get();
        $one = Order::where(['id' => $id])->first();
        return view('pages.order.view', compact('data', 'one'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("order")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = Order::where(['id' => $id])->first();
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
            $data = Order::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            /// update to product master stock
            $product = MasterProduct::where(['id' => $data->product_id])->first();
            $stock =  isset($product->stocks) ? json_decode($product->stocks, TRUE) : [];
            $stock[$data->channel] = isset($stock[$data->channel]) ? $stock[$data->channel] : 0;
            $stock[$data->channel] += $data->qty;
            $product->stocks = json_encode($stock);
            $product->save();

            return redirect()->route('order')
                ->with('message_success', 'Delete data success.');
        }
    }

    public function export(Request $request)
    {
        $loginner = Auth::user();
        $filename = "DATA-SELLER";
        $data = Order::where([
            'active' => 1,
            'user_id' => $loginner->id
        ])
            ->orderBy('created_at', 'desc')
            ->get();
        $excel = Excel::download(new HtmlExport($data, 'pages.order.excel'), sprintf("%s_%s.xlsx", $filename, date("dmYHis")));
        return $excel;
    }
}
