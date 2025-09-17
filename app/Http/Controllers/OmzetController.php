<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Constant;
use App\Models\Invoice;
use App\Models\Master;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OmzetController extends Controller
{
    //
    public function index(Request $request)
    {
        $request->year = $request->year ?? date("Y");
        $request->month = $request->month ?? date("n");
        $response = Invoice::get_all($request, true);
        $ms_months = Master::Months();
        $ms_years = Master::Years();
        $ms_payment_methods = Master::PaymentMethods();
        $ms_invoice_statuses = Master::InvoiceStatuses();
        $ms_branches = Branch::where(['active' => 1])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.omzet.view', compact(
            'response',
            'ms_months',
            'ms_years',
            'ms_branches',
            'ms_payment_methods',
            'ms_invoice_statuses',
            'request'
        ));
    }
}
