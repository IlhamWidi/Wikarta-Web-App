<?php

namespace App\Http\Controllers;

use App\Models\MarketPlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarketplaceController extends Controller
{
    //
    public function index(Request $request)
    {
        $data = MarketPlace::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.marketplace.view', compact('data'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("marketplace")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = new MarketPlace();
                $data->id = Str::uuid();
                $data->code = MarketPlace::generate_code();
                $data->name = $request->name;
                $data->save();
                DB::commit();
                return redirect()->route('marketplace')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('marketplace')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $data = MarketPlace::where([
            'active' => 1,
        ])
            ->get();
        $one = MarketPlace::where(['id' => $id])->first();
        return view('pages.marketplace.view', compact('data', 'one'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("marketplace")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = MarketPlace::where(['id' => $id])->first();
                $data->name = $request->name;
                $data->save();
                DB::commit();
                return redirect()->route('marketplace')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('marketplace')
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
            return redirect()->route('marketplace')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            $data = MarketPlace::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            return redirect()->route('marketplace')
                ->with('message_success', 'Delete data success.');
        }
    }
}
