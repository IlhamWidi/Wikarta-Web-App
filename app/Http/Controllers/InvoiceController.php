<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Constant;
use App\Models\Invoice;
use App\Models\InvoiceSetting;
use App\Models\Master;
use App\Models\Notification;
use App\Models\RoleAccessMenu;
use App\Models\Template;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use PDF;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
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

        $this->middleware(function ($request, $next) {

            $user = Auth::user();
            $title = $request->segment(1);
            $title = str_replace("-", " ", $title);
            $title = ucwords($title);

            $menus = collect();
            if (isset($user)) {
                // Ambil akses menu user
                $roleAccess = RoleAccessMenu::where('user_id', $user->id)->first();
                $allowedMenuIds = [];
                if ($roleAccess && is_array($roleAccess->menu_access)) {
                    $allowedMenuIds = $roleAccess->menu_access;
                } elseif ($roleAccess && is_string($roleAccess->menu_access)) {
                    $allowedMenuIds = json_decode($roleAccess->menu_access, true) ?? [];
                }

                // Ambil menu yang diizinkan
                $menus = \App\Models\Menu::where('active', 1)
                    ->whereIn('id', $allowedMenuIds)
                    ->orderBy('sequence', 'asc')
                    ->get();

                $auth = User::where(['id' => $user->id])->first();
                if (isset($auth)) {
                    View::share([
                        "name" => $auth["name"],
                        "username" => $auth["username"],
                        "role_id" => $auth["role_id"],
                        "title" => $title,
                        "menus" => $menus,
                    ]);
                }
            }
            return $next($request);
        });
    }

    //
    public function index(Request $request)
    {
        $request->year = $request->year ?? date("Y");
        $request->month = $request->month ?? date("n");

        // Tambahkan filter branch berdasarkan allowed_branches user
        $user = Auth::user();
        if ($user && !empty($user->allowed_branches)) {
            $request->allowed_branches = $user->allowed_branches;
        }

        $response = Invoice::get_all($request);
        $data = $response['data'] ?? [];
        $ms_months = Master::Months();
        $ms_years = Master::Years();
        $ms_payment_methods = Master::PaymentMethods();
        $ms_invoice_statuses = Master::InvoiceStatuses();
        $ms_branches = Branch::where(['active' => 1])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.invoice.view', compact(
            'data',
            'ms_months',
            'ms_years',
            'ms_branches',
            'ms_payment_methods',
            'ms_invoice_statuses',
            'request'
        ));
    }

    public function create(Request $request)
    {
        $ms_branches = Branch::where(['active' => 1])
            ->orderBy('created_at', 'asc')
            ->get();
        $ms_users = User::where([
            'active' => 1,
            'user_type' => Constant::USER_TYPE_CUSTOMER
        ])
            ->orderBy('created_at', 'asc')
            ->get();
        $ms_payment_methods = Master::PaymentMethods();
        $ms_invoice_statuses = Master::InvoiceStatuses();

        return view('pages.invoice.form', compact(
            'ms_branches',
            'ms_users',
            'ms_payment_methods',
            'ms_invoice_statuses',
        ));
    }

    public function store(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'due_date' => 'required|date',
            'amount' => 'required',
            'invoice_description' => 'required',
            'payment_method' => 'required',
            'invoice_status' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("invoice.create")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {
                DB::beginTransaction();

                $data = Invoice::where(['user_id' => $request->user_id])
                    ->whereRaw('month(due_date)=' . date('n'))
                    ->whereRaw('year(due_date)=' . date('Y'))
                    ->first();

                if (!isset($data)) {

                    $month = date('n');
                    $year = date('Y');
                    $data = new Invoice();
                    $data->id = Str::uuid();
                    $data->user_id = $request->user_id;
                    $data->month = $month;
                    $data->year = $year;
                }

                $user = User::where(['id' => $data->user_id])->first();
                $data->branch_id = $user->branch_id;
                $data->due_date = date("Y-m-d", strtotime($request->due_date));
                if (isset($request->paid_off_date)) {
                    $data->paid_off_date = date("Y-m-d", strtotime($request->paid_off_date));
                } else if ($request->invoice_status == Constant::INV_STATUS_PAID) {
                    return redirect()->route('invoice.create')
                        ->withInput($request->all())
                        ->with('message_error', 'Tanggal paid harus diisi!.');
                }

                $data->last_notification_date = date("Y-m-d");
                $data->invoice_number = "INV-" . time() . "-" . $user->id;
                $data->invoice_description = $request->invoice_description;
                $data->amount = floatval(str_replace(",", "", $request->amount));
                $data->invoice_status = $request->invoice_status;
                $data->payment_method = $request->payment_method;
                $data->save();
                $setting = InvoiceSetting::where(['active' => 1])->first();

                if ($data->invoice_status == Constant::INV_STATUS_PAID) {

                    //send notification paid
                    $template = $setting->notification_template_paid;
                    $message = str_replace("[nama]", $user->name, $template);
                    $message = str_replace("[paket]", $data->invoice_description, $message);
                    $message = str_replace("[nominal]", "Rp." . number_format($data->amount), $message);
                    $message = str_replace("[link]", route("invoice.print", ["id" => $data->id]), $message);
                    // $message = str_replace("[tanggal]", date("d-m-Y", strtotime($data->due_date)), $message);
                    $response = Notification::GoSendMessage($user->phone_number, $message);
                    // $response = Notification::SendMessage($user->phone_number, $message);
                    Log::info(json_encode($response));
                } else {
                    $invoice = Invoice::where(['id' => $data->id])->first();
                    $invoice->last_notification_date = date("Y-m-d");
                    $invoice->save();

                    $payload = [
                        'transaction_details' => [
                            'order_id' => $data->invoice_number,
                            'gross_amount' => $data->amount,
                        ]
                    ];
                    //$template = $setting->notification_template_invoice;
                    $link = Snap::getSnapUrl($payload);

                    if ($user->branches->code == "BRC-00000005") {
                        $template = Template::TEMPLATE_WA_INVOICE_PACITAN;
                        $message = str_replace("[name]", $user->name, $template);
                        $message = str_replace("[amount]", "Rp." . number_format($data->amount), $message);
                        $message = str_replace("[due_date]", date("d-m-Y", strtotime($data->due_date)), $message);
                        $message = str_replace("[invoice_link]", $link, $message);
                        $response = Notification::GoSendMessage($user->phone_number, $message);
                        //$response = Notification::SendMessage($user->phone_number, $message);
                    } else {
                        $template = Template::TEMPLATE_WA_INVOICE_UMUM;
                        $message = str_replace("[nama]", $user->name, $template);
                        $message = str_replace("[paket]", $data->invoice_description, $message);
                        $message = str_replace("[nominal]", "Rp." . number_format($data->amount), $message);
                        $message = str_replace("[tanggal]", date("d-m-Y", strtotime($data->due_date)), $message);
                        $message = str_replace("[link]", $link, $message);

                        $response = Notification::GoSendMessage($user->phone_number, $message);
                        // $response = Notification::SendMessage($user->phone_number, $message);
                    }
                    Log::info(json_encode($response));
                }

                DB::commit();
                return redirect()->route('invoice')->with('message_success', 'Data created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('invoice.create')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
        return view('pages.invoice.form');
    }

    public function edit(Request $request)
    {
        $rules = [
            "id" => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Id is required!'
            ]);
        } else {
            $data = Invoice::where(['id' => $request->id])
                ->with('users', 'branches')
                ->first();
            return response()->json([
                'error' => false,
                'data' => $data
            ]);
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => 'required',
            'payment_method' => 'required',
            'invoice_status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("invoice")
                ->withErrors($validator)
                ->withInput($request->all());
        } else {
            try {

                DB::beginTransaction();
                $data = Invoice::where(['id' => $request->id])->first();
                $data->payment_method = $request->payment_method;
                $data->invoice_status = $request->invoice_status;
                if ($data->invoice_status == Constant::INV_STATUS_PAID) {
                    if ($data->paid_off_date == null) {
                        $data->paid_off_date = date("Y-m-d");
                    }
                    //send notification paid
                    $setting = InvoiceSetting::where(['active' => 1])->first();
                    $template = $setting->notification_template_paid;

                    $message = str_replace("[nama]", $data->users->name, $template);
                    $message = str_replace("[paket]", $data->invoice_description, $message);
                    $message = str_replace("[nominal]", "Rp." . number_format($data->amount), $message);
                    $message = str_replace("[tanggal]", date("d-m-Y", strtotime($data->due_date)), $message);
                    $message = str_replace("[link]", route("invoice.print", ["id" => $data->id]), $message);

                    $response = Notification::GoSendMessage($data->users->phone_number, $message);
                    // $response = Notification::SendMessage($data->users->phone_number, $message);
                    Log::info(json_encode($response));

                }
                $data->save();

                DB::commit();
                return redirect()->route('invoice')->with('message_success', 'Data updated successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('invoice')
                    ->withInput($request->all())
                    ->with('message_error', 'Error exception!! ' . $e->getMessage());
            }
        }
    }

    public function print(Request $request, $id)
    {
        $data = Invoice::where(['id' => $request->id])
            ->with('users', 'branches')
            ->first();
        $filename = $data->invoice_number ?? "INVOICE";
        $pdf = PDF::loadView('pages.invoice.export', compact(
            'data',
            'filename',
        ))->setPaper('a4', 'potrait');

        // // return $pdf->download($filename . ".pdf");
        return $pdf->stream($filename . ".pdf");
        // return view('pages.invoice.export', compact('data', 'filename'));
    }

    public function export(Request $request)
    {
        $request->year = $request->year ?? date("Y");
        $request->month = $request->month ?? date("n");
        $user = Auth::user();
        if ($user && !empty($user->allowed_branches)) {
            $request->allowed_branches = $user->allowed_branches;
        }
        $response = \App\Models\Invoice::get_all($request);
        $data = $response['data'] ?? [];

        // Export as CSV
        $filename = 'invoice_export_' . $request->year . '_' . $request->month . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $columns = ['No', 'Branch', 'Invoice No', 'Customer', 'Due Date', 'Paid Date', 'Amount', 'Status'];
        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($data as $k => $v) {
                fputcsv($file, [
                    $k + 1,
                    $v->branches->name ?? '-',
                    $v->invoice_number,
                    $v->users->name ?? '-',
                    isset($v->due_date) ? date('d-m-Y', strtotime($v->due_date)) : '-',
                    isset($v->paid_off_date) ? date('d-m-Y', strtotime($v->paid_off_date)) : '-',
                    $v->amount,
                    $v->invoice_status
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
