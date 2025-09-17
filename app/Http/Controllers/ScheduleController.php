<?php

namespace App\Http\Controllers;

use App\Models\Constant;
use App\Models\Invoice;
use App\Models\InvoiceSetting;
use App\Models\Notification as Notif;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class ScheduleController extends Controller
{
    /**
     * Class constructor.
     *
     * @param \Illuminate\Http\Request $request User Request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        // Set midtrans configuration
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');
    }


    //
    public function invoice_task(Request $request)
    {
        // $data = User::where([
        //     'users.active' => 1,
        //     'users.status' => 1,
        //     'user_type' => Constant::USER_TYPE_CUSTOMER
        // ])
        //     ->select('users.*')
        //     // ->leftJoin('invoices', 'invoices.user_id', '=', 'users.id', "invoices.due_date between '" . date("Y-m-d") . "' AND '" . date("Y-m-t") . "'")
        //     ->get();
        // $invoices = Invoice::whereRaw('month(invoices.due_date)=' . date("n"))->orderBy('created_at', 'asc')->get();
        // $invoice_kv = [];
        // foreach ($invoices as $k => $v) {
        //     $invoice_kv[$v->user_id] = $v;
        // }
        // $setting = InvoiceSetting::where(['active' => 1])->first();

        // foreach ($data as $k => $v) {
        //     if (!isset($invoice_kv[$v->user_id])) {

        //         $due_date = str_pad($setting->invoice_date, 2, "0", STR_PAD_LEFT);
        //         $invoice = new Invoice();
        //         $invoice->id = Str::uuid();
        //         $invoice->user_id = $v->id;
        //         $invoice->due_date = date("Y-m-15");
        //         $invoice->invoice_number = "INV-" . time() . "-" . $v->id;
        //         $invoice->invoice_description = "Tagihan pembayaran internet " . $v->packages->name . " " . $v->packages->description;
        //         $invoice->amount = $v->subscribe_price;
        //         $invoice->branch_id = $v->branch_id;
        //         $invoice->invoice_status = Constant::INV_STATUS_UNPAID;
        //         $invoice->save();
        //     }
        // }
        // return response()->json($data);
    }

    public function invoice_notification(Request $request)
    {
        $setting = InvoiceSetting::where(['active' => 1])->first();
        $template = $setting->notification_template_invoice;
        $data = Invoice::where('last_notification_date', '=', null)->limit(10)->get();

        foreach ($data as $k => $v) {

            $invoice = Invoice::where(['id' => $v->id])->first();
            $invoice->last_notification_date = date("Y-m-d");
            $invoice->save();

            $payload = [
                'transaction_details' => [
                    'order_id' => $v->invoice_number,
                    'gross_amount' => $v->amount,
                ]
            ];

            $link = Snap::getSnapUrl($payload);
            $message = str_replace("[nama]", $v->users->name, $template);
            $message = str_replace("[paket]", $v->invoice_description, $message);
            $message = str_replace("[nominal]", "Rp." . number_format($v->amount), $message);
            $message = str_replace("[tanggal]", date("15-m-Y"), $message);
            $message = str_replace("[link]", $link, $message);

            $result = Notif::GoSendMessage($v->users->phone_number, $message);
            // $result = Notif::SendMessage($v->users->phone_number, $message);
            Log::info(json_encode($result));
        }

        return response()->json($data);
    }
}
