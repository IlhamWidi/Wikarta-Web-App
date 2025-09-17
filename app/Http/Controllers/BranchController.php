<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BranchController extends Controller
{
    //
    public function index(Request $request)
    {
        $data = Branch::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.branch.view', compact('data'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("branch")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = new Branch();
                $data->id = Str::uuid();
                $data->code = Branch::generate_code();
                $data->name = $request->name;
                $data->save();
                DB::commit();
                return redirect()->route('branch')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('branch')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $data = Branch::where([
            'active' => 1,
        ])
            ->get();
        $one = Branch::where(['id' => $id])->first();
        return view('pages.branch.view', compact('data', 'one'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("branch")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = branch::where(['id' => $id])->first();
                $data->name = $request->name;
                $data->save();
                DB::commit();
                return redirect()->route('branch')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('branch')
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
            return redirect()->route('branch')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            $data = Branch::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            return redirect()->route('branch')
                ->with('message_success', 'Delete data success.');
        }
    }
}
