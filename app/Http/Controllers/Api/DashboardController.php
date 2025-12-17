<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function stats(Request $request)
    {
        try {
            // Total Customers
            $totalCustomers = Customer::count();

            // Total Revenue (from paid payments)
            $totalRevenue = Payment::where('status', 'paid')->sum('amount');

            // Pending Invoices
            $pendingInvoices = Invoice::where('status', 'pending')->count();

            // Paid Invoices
            $paidInvoices = Invoice::where('status', 'paid')->count();

            // Active Subscriptions
            $activeSubscriptions = DB::table('subscriptions')
                ->where('status', 'active')
                ->count();

            // Monthly Revenue (current month)
            $monthlyRevenue = Payment::where('status', 'paid')
                ->whereYear('paid_at', date('Y'))
                ->whereMonth('paid_at', date('m'))
                ->sum('amount');

            // Recent Customers (last 7 days)
            $recentCustomers = Customer::where('created_at', '>=', now()->subDays(7))
                ->count();

            // Overdue Invoices
            $overdueInvoices = Invoice::where('status', 'overdue')
                ->orWhere(function($query) {
                    $query->where('status', 'pending')
                          ->where('due_date', '<', now());
                })
                ->count();

            return response()->json([
                'totalCustomers' => $totalCustomers,
                'totalRevenue' => $totalRevenue,
                'pendingInvoices' => $pendingInvoices,
                'paidInvoices' => $paidInvoices,
                'activeSubscriptions' => $activeSubscriptions,
                'monthlyRevenue' => $monthlyRevenue,
                'recentCustomers' => $recentCustomers,
                'overdueInvoices' => $overdueInvoices,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch dashboard stats',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function recentActivity(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);

            $recentInvoices = Invoice::with(['customer'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            $recentPayments = Payment::with(['invoice.customer'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            $recentCustomers = Customer::orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'recentInvoices' => $recentInvoices,
                'recentPayments' => $recentPayments,
                'recentCustomers' => $recentCustomers,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch recent activity',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
