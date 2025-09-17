<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Master;
use App\Models\MasterProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    //
    public function index(Request $request)
    {
        $ms_category = Category::where(['active' => 1])->get();
        $ms_marketplace = Master::MarketPlace();
        $data = MasterProduct::where(['active' => 1])->get();
        foreach ($data as $k => $v) {
            $v->stock_list = json_decode($v->stocks, TRUE);
            $data[$k] = $v;
        }
        return view('pages.product.view', compact('ms_category', 'ms_marketplace', 'data'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'category_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("product")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = new MasterProduct();
                $data->id = Str::uuid();
                $data->name = $request->name;
                $data->barcode = $request->barcode;
                $data->category_id = $request->category_id;
                $marketplace = Master::MarketPlace();
                $admin_fees = [];
                $mp_prices = [];

                foreach ($marketplace as $k => $v) {
                    $admin_fees[$v] = $request->get("potongan_" . $v) ?? null;
                    $mp_prices[$v] = $request->get("price_" . $v) ??  null;
                }

                $data->admin_fees = json_encode($admin_fees);
                $data->mp_prices  = json_encode($mp_prices);
                $data->save();
                DB::commit();
                return redirect()->route('product')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('product')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $data = MasterProduct::where([
            'active' => 1,
        ])
            ->get();

        $one = MasterProduct::where(['id' => $id])->first();
        $admin_fees =  json_decode($one->admin_fees, TRUE);
        $mp_prices =  json_decode($one->mp_prices, TRUE);
        $ms_category = Category::where(['active' => 1])->get();
        $ms_marketplace = Master::MarketPlace();

        return view('pages.product.view', compact(
            'ms_category',
            'ms_marketplace',
            'data',
            'one',
            'admin_fees',
            'mp_prices'
        ));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'category_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("product.edit", ["id" =>  $id])
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {
                DB::beginTransaction();
                $data = MasterProduct::where(['id' => $id])->first();
                $data->name = $request->name;
                $data->barcode = $request->barcode;
                $data->category_id = $request->category_id;
                $marketplace = Master::MarketPlace();
                $admin_fees = [];
                $mp_prices = [];

                foreach ($marketplace as $k => $v) {
                    $admin_fees[$v] = $request->get("potongan_" . $v) ?? null;
                    $mp_prices[$v] = $request->get("price_" . $v) ??  null;
                }

                $data->admin_fees = json_encode($admin_fees);
                $data->mp_prices  = json_encode($mp_prices);

                $data->save();
                DB::commit();
                return redirect()->route("product")->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route("product.edit", ["id" => $id])
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }
}
