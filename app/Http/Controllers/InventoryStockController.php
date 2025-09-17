<?php

namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\Master;
use App\Models\MasterProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Export\HtmlExport;

class InventoryStockController extends Controller
{
    //
    public function index(Request $request)
    {
        $data = InventoryStock::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'asc')
            ->get();

        $ms_product = MasterProduct::where(['active' => 1])->get();
        $ms_channel = Master::MarketPlace();

        return view('pages.inventorystock.view', compact('data', 'ms_product', 'ms_channel'));
    }

    public function store(Request $request)
    {
        $rules = [
            'product_id' => 'required',
            'channel' => 'required',
            'qty' => 'required',
            'price' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("inventorystock")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = new InventoryStock();
                $data->id = Str::uuid();
                $data->product_id = $request->product_id;
                $data->channel = $request->channel;
                $data->qty = $request->qty;
                $data->price = $request->price;
                $data->save();

                /// update to product master stock
                $product = MasterProduct::where(['id' => $request->product_id])->first();
                $stock =  isset($product->stocks) ? json_decode($product->stocks, TRUE) : [];
                $stock[$request->channel] = isset($stock[$request->channel]) ? $stock[$request->channel] : 0;
                $stock[$request->channel] += $request->qty;
                $product->stocks = json_encode($stock);
                $product->save();

                DB::commit();
                return redirect()->route('inventorystock')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('inventorystock')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $data = InventoryStock::where([
            'active' => 1,
        ])
            ->get();
        $one = InventoryStock::where(['id' => $id])->first();
        $ms_product = MasterProduct::where(['active' => 1])->get();
        $ms_channel = Master::MarketPlace();

        return view('pages.inventorystock.view', compact('data', 'one', 'ms_product', 'ms_channel'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'price' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("inventorystock")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = InventoryStock::where(['id' => $id])->first();
                $data->price = $request->price;
                $data->save();
                DB::commit();
                return redirect()->route('inventorystock')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('inventorystock')
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
            return redirect()->route('inventorystock')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            $data = InventoryStock::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            /// update to product master stock
            $product = MasterProduct::where(['id' => $data->product_id])->first();
            $stock =  isset($product->stocks) ? json_decode($product->stocks, TRUE) : [];
            $stock[$data->channel] = isset($stock[$data->channel]) ? $stock[$data->channel] : 0;
            $stock[$data->channel] -= $data->qty;
            $product->stocks = json_encode($stock);
            $product->save();

            return redirect()->route('inventorystock')
                ->with('message_success', 'Delete data success.');
        }
    }

    public function export(Request $request)
    {
        $filename = "DATA-PURCHASE-ORDER";
        $data = InventoryStock::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'asc')
            ->get();
        $excel = Excel::download(new HtmlExport($data, 'pages.inventorystock.excel'), sprintf("%s_%s.xlsx", $filename, date("dmYHis")));
        return $excel;
    }
}
