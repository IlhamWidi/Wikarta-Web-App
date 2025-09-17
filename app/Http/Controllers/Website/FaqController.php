<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FaqController extends Controller
{
    //
    public function index(Request $request)
    {
        $data = Faq::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.website.faq.view', compact('data'));
    }

    public function store(Request $request)
    {
        $rules = [
            'topic' => 'required',
            'question' => 'required',
            'answer' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("website-faq")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();

                $data = new Faq();
                $data->id = Str::uuid();
                $data->topic = $request->topic;
                $data->question = $request->question;
                $data->answer = $request->answer;
                $data->save();

                DB::commit();
                return redirect()->route('website-faq')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('website-faq')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $data = Faq::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'asc')
            ->get();

        $one = Faq::where(['id' => $id])->first();
        return view('pages.website.faq.view', compact('data', 'one'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'topic' => 'required',
            'question' => 'required',
            'answer' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("website-faq")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = Faq::where(['id' => $id])->first();
                $data->topic = $request->topic;
                $data->question = $request->question;
                $data->answer = $request->answer;
                $data->save();

                DB::commit();
                return redirect()->route('website-faq')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('website-faq')
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
            return redirect()->route('website-faq')
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            $data = Faq::where(['id' => $request->id])->first();
            $data->active = 0;
            $data->save();

            return redirect()->route('website-faq')
                ->with('message_success', 'Delete data success.');
        }
    }
}
