<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    //
    public function index(Request $request)
    {
        $data = Category::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.category.view', compact('data'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("Category")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = new Category();
                $data->id = Str::uuid();
                $data->name = $request->name;
                $data->save();
                DB::commit();
                return redirect()->route('category')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('category')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $data = Category::where([
            'active' => 1,
        ])
            ->get();
        $one = Category::where(['id' => $id])->first();
        return view('pages.category.view', compact('data', 'one'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("Category")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = Category::where(['id' => $id])->first();
                $data->name = $request->name;
                $data->save();
                DB::commit();
                return redirect()->route('category')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('category')
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
            return redirect()->route('category')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            $data = Category::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            return redirect()->route('category')
                ->with('message_success', 'Delete data success.');
        }
    }
}
