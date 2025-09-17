<?php

namespace App\Http\Controllers;

use App\Models\Constant;
use App\Models\Helper;
use App\Models\Invoice;
use App\Models\Master;
use App\Models\Midtrans;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Midtrans\Config;

class LandingController extends Controller
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
    public function payment($id)
    {
        $month = date('n');
        $year = date('Y');

        $invoice = Invoice::where(['id' => $id])->first();
        $ms_metode_pembayaran = PaymentMethod::orderBy('id', 'asc')->get();
        $ms_month = Master::Months();

        $id = $invoice->id;
        $data = $invoice;
        $data->periode = $ms_month[$data->month] . "-" . $data->year;

        return view('pages.landing.form', compact(
            'id',
            'data',
            'ms_metode_pembayaran'
        ));
    }

    public function process_payment(Request $request, $id)
    {
        $rules = [
            'metode_pembayaran' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("landing.payment", ["id" => $id])
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $invoice = Invoice::where(['id' => $id])->first();
                $invoice->payment_method = Constant::PAYMENT_MIDTRANS;
                $invoice->method_id = $request->metode_pembayaran;
                if ($invoice->invoice_number == null || $invoice->invoice_number == "") {
                    $invoice->invoice_number = "INV-" . time() . "-" . $invoice->users->id;
                }
                // $invoice->invoice_number = "INV-" . time() . "-" . $invoice->users->id;
                $invoice->save();
                DB::commit();

                $method = PaymentMethod::where(['id' => $request->metode_pembayaran])->first();

                // exec midtrans
                $body = [
                    "payment_type" => $method->midtrans_code,
                    "transaction_details" => [
                        "order_id" => $invoice->invoice_number,
                        "gross_amount" => $invoice->amount
                    ],
                ];

                if ($method->midtrans_code == "bank_transfer") {
                    $body["bank_transfer"] = [
                        "bank" => $method->bank_code
                    ];
                } else if ($method->midtrans_code == "echannel") {
                    $body["echannel"] = [
                        "bill_info1" => $invoice->invoice_description,
                        "bill_info2" => "Online payment"
                    ];
                } else if ($method->midtrans_code == "cstore") {
                    $body["cstore"] = [
                        "store" => $invoice->bank_code,
                        "message" => $invoice->invoice_description
                    ];
                }

                $payload = [
                    'transaction_details' => [
                        "order_id" => $invoice->invoice_number,
                        "gross_amount" => $invoice->amount
                    ]
                ];
                if ($method->midtrans_code == "cstore") {
                    $link = Snap::getSnapUrl($payload);
                    $link = $link . "#" . $method->link;
                    return redirect()->to($link);
                } else if ($method->midtrans_code == "bank_transfer") {
                    $link = Snap::getSnapUrl($payload);
                    $link = $link . "#" . $method->link;
                    return redirect()->to($link);
                } else if ($method->midtrans_code == "echannel") {
                    $link = Snap::getSnapUrl($payload);
                    $link = $link . "#" . $method->link;
                    return redirect()->to($link);
                } else if ($method->midtrans_code == "gopay") {
                    $link = Snap::getSnapUrl($payload);
                    $link = $link . "#" . $method->link;
                    return redirect()->to($link);
                } else if ($method->midtrans_code == "permata") {
                    $link = Snap::getSnapUrl($payload);
                    $link = $link . "#" . $method->link;
                    return redirect()->to($link);
                }

                $response = Midtrans::bank_transfer("charge", $body);
                $invoice->request = json_encode($body);
                $invoice->response = json_encode($response);

                if ($method->midtrans_code == "bank_transfer") {
                    $invoice->va_number = $response["va_numbers"][0]["va_number"];
                } else if ($method->midtrans_code == "echannel") {
                    $invoice->va_key = $response["biller_code"];
                    $invoice->va_number = $response["bill_key"];
                } else if ($method->midtrans_code == "gopay") {
                    $invoice->image_qr = $response['actions'][0]['url'];
                }

                $invoice->expired_at = date("Y-m-d H:i:s", strtotime($response['expiry_time'] . " -2minutes"));
                $invoice->save();
                DB::commit();

                return redirect()->route('landing.waiting-payment', [
                    'id' => $id,
                    "method" => $request->metode_pembayaran
                ])->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route("landing.payment", ["id" => $id])
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function waiting_payment($id, $method)
    {
        $invoice = Invoice::where(['id' => $id])->first();
        $method = PaymentMethod::where(['id' => $method])->first();
        $duration = Helper::DateDiff2Dates(date($invoice->expired_at), date("Y-m-d H:i:s"));

        return view('pages.landing.process', compact(
            'id',
            'invoice',
            'method',
            'duration'
        ));
    }
}
