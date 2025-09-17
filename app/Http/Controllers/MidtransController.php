<?php

namespace App\Http\Controllers;

use App\Models\Constant;
use App\Models\Invoice;
use App\Models\InvoiceSetting;
use App\Models\Notification;
use Illuminate\Http\Request;
use Log;

class MidtransController extends Controller
{
    //
    public function notification_handler(Request $request)
    {
        if (isset($request) && isset($request->transaction_status)) {
            if ($request->transaction_status == "settlement") {

                $invoice = Invoice::where(['invoice_number' => $request->order_id])->first();
                if (isset($invoice) && $invoice->invoice_status == Constant::INV_STATUS_UNPAID) {
                    $invoice->request = json_encode($request->all());
                    $invoice->payment_method = Constant::PAYMENT_MIDTRANS;
                    $invoice->invoice_status = Constant::INV_STATUS_PAID;
                    $invoice->paid_off_date = date("Y-m-d");
                    $invoice->save();

                    $setting = InvoiceSetting::where(['active' => 1])->first();
                    $template = $setting->notification_template_paid;

                    $message = str_replace("[nama]", $invoice->users->name, $template);
                    $message = str_replace("[paket]", $invoice->invoice_description, $message);
                    $message = str_replace("[nominal]", "Rp." . number_format($invoice->amount), $message);
                    $message = str_replace("[tanggal]", date("d-m-Y", strtotime($invoice->due_date)), $message);
                    $message = str_replace("[link]", route("invoice.print", ["id" => $invoice->id]), $message);

                    $response = Notification::GoSendMessage($invoice->users->phone_number, $message);
                    // $response = Notification::SendMessage($invoice->users->phone_number, $message);
                    Log::info(json_encode($response));

                } else {
                    return response()->json(["message" => "ok"]);
                }
            }
        }
        return response()->json(["message" => "ok"]);
    }
}
