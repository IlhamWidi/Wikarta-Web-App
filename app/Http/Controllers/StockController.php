<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Master;
use App\Models\MasterProduct;

class StockController extends Controller
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
        return view('pages.kasir.stock', compact('ms_category', 'ms_marketplace', 'data'));
    }
}
