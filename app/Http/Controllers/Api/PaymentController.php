<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Services\AuditLogService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_payments')->only(['index', 'show']);
        $this->middleware('permission:create_payments')->only('store');
        $this->middleware('permission:verify_payments')->only('verify');
    }

    public function index(Request $request)
    {
        $query = Payment::with(['invoice.customer', 'customer', 'verifier']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by invoice
        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        // Date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_code', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhere('va_number', 'like', "%{$search}%");
            });
        }

        $allowedSortColumns = ['id', 'payment_code', 'amount', 'status', 'payment_method', 'created_at', 'settlement_time'];
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, $allowedSortColumns) && in_array($sortOrder, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->get('per_page', 15);
        $payments = $query->paginate($perPage);

        return response()->json($payments);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'payment_method' => 'required|in:bank_transfer,virtual_account,qris,credit_card,e_wallet,cash,other',
            'payment_type' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'bank' => 'nullable|string|in:bca,bni,bri,permata,mandiri',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $invoice = Invoice::findOrFail($request->invoice_id);

        // Check if invoice can accept payment
        if (in_array($invoice->status, ['void', 'refund', 'paid'])) {
            return response()->json([
                'message' => 'Cannot create payment for invoice with status: ' . $invoice->status
            ], 422);
        }

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'payment_method' => $request->payment_method,
                'payment_type' => $request->payment_type,
                'amount' => $request->amount,
                'status' => 'pending',
                'notes' => $request->notes,
                'metadata' => [
                    'invoice_order_id' => $invoice->order_id,
                ],
            ]);

            // Initialize Midtrans Service
            $midtransService = new MidtransService();
            $midtransResult = [];

            // Create transaction based on payment method
            if ($request->payment_method === 'virtual_account') {
                $bank = $request->bank ?? 'bca';
                $midtransResult = $midtransService->createVirtualAccount($payment, $bank);
            } elseif ($request->payment_method === 'qris') {
                $midtransResult = $midtransService->createQRIS($payment);
            } else {
                // Use Snap for other methods (credit card, e-wallet, etc)
                $midtransResult = $midtransService->createSnapToken($payment);
            }

            if (!$midtransResult['success']) {
                throw new \Exception('Midtrans transaction failed: ' . $midtransResult['error']);
            }

            $payment->refresh();

            AuditLogService::log($payment, 'payment.created', [
                'new' => [
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                ],
                'metadata' => [
                    'order_id' => $payment->order_id,
                    'midtrans' => $midtransResult,
                ],
                'description' => 'Payment created via dashboard',
            ]);

            // Update invoice status
            $invoice->update([
                'status' => 'pending',
                'updated_by' => $request->user()?->id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Payment created successfully',
                'data' => $payment->fresh(['invoice', 'customer']),
                'payment_info' => $midtransResult,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $payment = Payment::with([
            'invoice.customer',
            'invoice.items',
            'customer',
            'verifier'
        ])->findOrFail($id);

        return response()->json([
            'data' => $payment
        ]);
    }

    public function verify(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        // Only pending payments can be verified
        if ($payment->status !== 'pending') {
            return response()->json([
                'message' => 'Can only verify pending payments'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:settlement,capture,deny,cancel',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $payment->update([
                'status' => $request->status,
                'verified_by' => $request->user()?->id,
                'verified_at' => now(),
                'settlement_time' => in_array($request->status, ['settlement', 'capture']) ? now() : null,
                'notes' => $request->notes,
            ]);

            $payment->refresh();

            AuditLogService::log($payment, 'payment.verified', [
                'old' => ['status' => 'pending'],
                'new' => ['status' => $request->status],
                'description' => 'Payment manually verified',
                'metadata' => ['notes' => $request->notes],
            ]);

            // Update invoice status based on payment status
            if (in_array($request->status, ['settlement', 'capture'])) {
                $payment->invoice()->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'updated_by' => $request->user()?->id,
                ]);
            } elseif (in_array($request->status, ['deny', 'cancel'])) {
                $payment->invoice()->update([
                    'status' => 'issued',
                    'updated_by' => $request->user()?->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Payment verified successfully',
                'data' => $payment->load(['invoice', 'verifier'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to verify payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);

        // Only pending payments can be deleted
        if ($payment->status !== 'pending') {
            return response()->json([
                'message' => 'Can only delete pending payments'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $payment->delete();
            DB::commit();

            return response()->json([
                'message' => 'Payment deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
