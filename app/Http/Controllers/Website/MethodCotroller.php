<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Helper;
use App\Models\Method;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MethodCotroller extends Controller
{
    //
    public function index(Request $request)
    {
        $data = Method::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.website.method.view', compact('data'));
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("website-method")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $image = null;
                if ($request->hasFile('image')) {
                    $ext = $request->file('image')->guessExtension();
                    $name = sprintf("%s_%s_payment", Helper::str_to_slug($request->title), time());
                    $image = "storage/" . $request->file('image')->storeAs('image', $name . '.' . $ext, 'public');
                }

                $data = new Method();
                $data->id = Str::uuid();
                $data->title = $request->title;
                $data->description = $request->description;
                $data->image = $image;
                $data->save();

                DB::commit();
                return redirect()->route('website-method')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('website-method')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $data = Method::where([
            'active' => 1,
        ])
            ->get();
        $one = Method::where(['id' => $id])->first();
        return view('pages.website.method.view', compact('data', 'one'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("website-method")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = Method::where(['id' => $id])->first();
                $image = $data->image;
                if ($request->hasFile('image')) {
                    $ext = $request->file('image')->guessExtension();
                    $name = sprintf("%s_%s_payment", Helper::str_to_slug($request->title), time());
                    $image = "storage/" . $request->file('image')->storeAs('image', $name . '.' . $ext, 'public');
                }

                $data->title = $request->title;
                $data->description = $request->description;
                $data->image = $image;
                $data->save();

                DB::commit();
                return redirect()->route('website-method')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('website-method')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function delete(Request $request)
    {
        $rules = [
            'id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('website-method')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            $data = Method::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            return redirect()->route('website-method')
                ->with('message_success', 'Delete data success.');
        }
    }
}
