<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Helper;
use App\Models\Partner;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PartnerController extends Controller
{
    //
    public function index(Request $request)
    {
        $data = Partner::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.website.partner.view', compact('data'));
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
            return redirect()->route("website-partner")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $image = null;
                if ($request->hasFile('image')) {
                    $ext = $request->file('image')->guessExtension();
                    $name = sprintf("%s_%s_partner", Helper::str_to_slug($request->title), time());
                    $image = "storage/" . $request->file('image')->storeAs('image', $name . '.' . $ext, 'public');
                }

                $data = new Partner();
                $data->id = Str::uuid();
                $data->title = $request->title;
                $data->description = $request->description;
                $data->image = $image;
                $data->save();

                DB::commit();
                return redirect()->route('website-partner')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('website-partner')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $data = Partner::where([
            'active' => 1,
        ])
            ->get();
        $one = Partner::where(['id' => $id])->first();
        return view('pages.website.partner.view', compact('data', 'one'));
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
            return redirect()->route("website-partner")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = Partner::where(['id' => $id])->first();
                $image = $data->image;
                if ($request->hasFile('image')) {
                    $ext = $request->file('image')->guessExtension();
                    $name = sprintf("%s_%s_partner", Helper::str_to_slug($request->title), time());
                    $image = "storage/" . $request->file('image')->storeAs('image', $name . '.' . $ext, 'public');
                }

                $data->title = $request->title;
                $data->description = $request->description;
                $data->image = $image;
                $data->save();

                DB::commit();
                return redirect()->route('website-partner')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('website-partner')
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
            return redirect()->route('website-partner')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            $data = Partner::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            return redirect()->route('website-partner')
                ->with('message_success', 'Delete data success.');
        }
    }
}
