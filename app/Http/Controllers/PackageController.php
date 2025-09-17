<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    //
    public function index(Request $request)
    {
        $data = Package::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.package.view', compact('data'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'subscribe_price' => 'required',
            'registration_price' => 'required',
            'spesification' => 'required',
            'sequence' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("package")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = new Package();
                $data->id = Str::uuid();
                $data->name = $request->name;
                $data->description = $request->description;
                $data->subscribe_price = floatval(str_replace(",", "",  $request->subscribe_price));
                $data->registration_price = floatval(str_replace(",", "",  $request->registration_price));
                $data->spesification = $request->spesification;
                $data->sequence = $request->sequence;
                $data->publish = $request->publish == "on" ? true : false;
                $data->save();
                DB::commit();
                return redirect()->route('package')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('package')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $data = Package::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'asc')
            ->get();
        $one = Package::where(['id' => $id])->first();
        return view('pages.package.view', compact('data', 'one'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'subscribe_price' => 'required',
            'registration_price' => 'required',
            'spesification' => 'required',
            'sequence' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("package")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = Package::where(['id' => $id])->first();
                $data->name = $request->name;
                $data->description = $request->description;
                $data->subscribe_price = floatval(str_replace(",", "",  $request->subscribe_price));
                $data->registration_price = floatval(str_replace(",", "",  $request->registration_price));
                $data->spesification = $request->spesification;
                $data->sequence = $request->sequence;
                $data->publish = $request->publish == "on" ? true : false;
                $data->save();
                DB::commit();
                return redirect()->route('package')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('package')
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
            return redirect()->route('package')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            $data = Package::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            return redirect()->route('package')
                ->with('message_success', 'Delete data success.');
        }
    }

    public function show(Request $request)
    {
        $rules = [
            "id" => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Id is required!'
            ]);
        } else {
            $data = Package::where(['id' => $request->id])->first();
            return response()->json([
                'error' => false,
                'data' => $data
            ]);
        }
    }
}
