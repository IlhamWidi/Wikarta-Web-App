<?php

namespace App\Http\Controllers;

use App\Models\Constant;
use App\Models\Invoice;
use App\Models\InvoiceSetting;
use App\Models\Template;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Notification as Notif;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Config;

class JobController extends Controller
{
    //
    public function generate_invoice(Request $request)
    {
        $month = date('n');
        $year = date('Y');
        $counter = 0;

        $merchant = User::where([
            'user_type' => Constant::USER_TYPE_CUSTOMER,
            'status' => 1,
            'active' => 1
        ])
            ->limit(10)
            ->where('collection_date', '<', date("Y-m-d H:i:s", strtotime("-24hours")))
            ->get();

        foreach ($merchant as $k => $v) {

            $invoice = Invoice::where([
                'month' => $month,
                "year" => $year,
                "user_id" => $v->id,
                "active" => 1
            ])
                ->first();

            if (!isset($invoice)) {

                $invoice = new Invoice();
                $invoice->id = Str::uuid();
                $invoice->user_id = $v->id;
                $invoice->month = $month;
                $invoice->year = $year;
                $invoice->due_date = date("Y-m-15");
                $invoice->invoice_number = "INV-" . time() . "-" . $v->id;
                $invoice->invoice_description = "Tagihan pembayaran internet " . $v->packages->name . " " . $v->packages->description;
                $invoice->amount = $v->subscribe_price;
                $invoice->branch_id = $v->branch_id;
                $invoice->invoice_status = Constant::INV_STATUS_UNPAID;
                $invoice->last_notification_date = date("Y-m-d", strtotime("-2 days"));
                $invoice->save();
                $counter++;
            }

            $v->collection_date = date("Y-m-d H:i:s");
            $v->save();
        }

        return response()->json(["message" => "successfully generate invoice " . $counter . " pelanggan"]);
    }

    public function generate_notification(Request $request)
    {
        $month = date('n');
        $year = date('Y');

        $data = Invoice::whereDate('last_notification_date', '<', now()->toDateString())
            ->where([
                'month' => $month,
                "year" => $year,
            ])
            ->where('invoice_status', Constant::INV_STATUS_UNPAID)
            ->where('active', 1)
            ->limit(5)
            ->get();

        foreach ($data as $k => $v) {

            $invoice = Invoice::where(['id' => $v->id])->first();
            $invoice->last_notification_date = date("Y-m-d");
            $invoice->save();

            $link = route('landing.payment', ['id' => $v->id]);

            if ($v->branches && $v->branches->name === 'Hadiwarno Lorok Pacitan') {
                $template = Template::TEMPLATE_WA_INVOICE_PACITAN;
                $message = str_replace("[name]", $v->users->name, $template);
                $message = str_replace("[amount]", "Rp." . number_format($v->amount), $message);
                $message = str_replace("[due_date]", date("d-m-Y", strtotime($v->due_date)), $message);
                $message = str_replace("[invoice_link]", $link, $message);
            } else {
                $template = Template::TEMPLATE_WA_INVOICE_UMUM;
                $message = str_replace("[nama]", $v->users->name, $template);
                $message = str_replace("[paket]", $v->invoice_description, $message);
                $message = str_replace("[nominal]", "Rp." . number_format($v->amount), $message);
                $message = str_replace("[tanggal]", date("15-m-Y"), $message);
                $message = str_replace("[link]", $link, $message);
            }



            $response = Notif::GoSendMessage($v->users->phone_number, $message);
            Log::info(json_encode($response));
        }

        return response()->json($data);
    }
}
