<?php

namespace App\Http\Controllers;

use App\Models\InvoiceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InvoiceSettingController extends Controller
{
    //
    public function index(Request $request)
    {
        $data = InvoiceSetting::where([
            'active' => 1,
        ])
            ->orderBy('created_at', 'asc')
            ->first();

        return view('pages.invoice-setting.view', compact('data'));
    }

    public function store(Request $request)
    {
        $rules = [
            'invoice_date' => 'required',
            'notification_days_before' => 'required',
            'notification_days_warning' => 'required',
            'notification_template_invoice' => 'required',
            'notification_template_paid' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("invoice-setting")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {
                $data = InvoiceSetting::where(['active' => 1])->first();
                if (!isset($data)) {
                    $data = new InvoiceSetting();
                    $data->id = Str::uuid();
                }
                $data->invoice_date = $request->invoice_date;
                $data->notification_days_before = $request->notification_days_before;
                $data->notification_days_warning = $request->notification_days_warning;
                $data->notification_template_invoice = $request->notification_template_invoice;
                $data->notification_template_paid = $request->notification_template_paid;
                $data->save();
                return redirect()->route('invoice-setting')->with('message_success', 'Data updated successfully.');
            } catch (\Exception $e) {
                return redirect()->route('invoice-setting')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }
}
