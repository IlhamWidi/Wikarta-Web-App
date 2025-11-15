<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\AuditLogService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_invoices')->only(['index', 'show']);
        $this->middleware('permission:create_invoices')->only('store');
        $this->middleware('permission:edit_invoices')->only('update');
        $this->middleware('permission:void_invoices')->only('void');
    }

    public function index(Request $request)
    {
        $query = Invoice::with(['customer', 'subscription.package', 'items', 'payments', 'creator']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter overdue
        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        // Filter unpaid
        if ($request->boolean('unpaid')) {
            $query->unpaid();
        }

        // Date range
        if ($request->has('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $allowedSortColumns = ['id', 'invoice_number', 'invoice_date', 'due_date', 'total', 'status', 'created_at'];
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, $allowedSortColumns) && in_array($sortOrder, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->get('per_page', 15);
        $invoices = $query->paginate($perPage);

        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'subscription_id' => 'nullable|exists:subscriptions,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = collect($request->items)->sum(function($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $tax = $request->get('tax', 0);
            $discount = $request->get('discount', 0);
            $total = $subtotal + $tax - $discount;

            // Create invoice
            $invoice = Invoice::create([
                'customer_id' => $request->customer_id,
                'subscription_id' => $request->subscription_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'status' => 'issued',
                'description' => $request->description,
                'notes' => $request->notes,
                'created_by' => $request->user()?->id,
            ]);

            // Create invoice items
            foreach ($request->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Invoice created successfully',
                'data' => $invoice->load(['customer', 'items', 'creator'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $invoice = Invoice::with([
            'customer',
            'subscription.package',
            'items',
            'payments',
            'creator',
            'updater',
            'voidedBy',
            'refundedBy'
        ])->findOrFail($id);

        return response()->json([
            'data' => $invoice
        ]);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Only allow update if status is draft or issued
        if (!in_array($invoice->status, ['draft', 'issued'])) {
            return response()->json([
                'message' => 'Cannot update invoice with status: ' . $invoice->status
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'due_date' => 'sometimes|required|date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            
            // Recalculate total if tax or discount changed
            if (isset($data['tax']) || isset($data['discount'])) {
                $tax = $data['tax'] ?? $invoice->tax;
                $discount = $data['discount'] ?? $invoice->discount;
                $data['total'] = $invoice->subtotal + $tax - $discount;
            }

            $data['updated_by'] = $request->user()?->id;
            $invoice->update($data);

            DB::commit();

            return response()->json([
                'message' => 'Invoice updated successfully',
                'data' => $invoice->load('updater')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function void(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Only allow void if status is not already void or paid
        if (in_array($invoice->status, ['void', 'paid', 'refund'])) {
            return response()->json([
                'message' => 'Cannot void invoice with status: ' . $invoice->status
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'void_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $previousStatus = $invoice->status;
            $invoice->update([
                'status' => 'void',
                'void_reason' => $request->void_reason,
                'voided_by' => $request->user()?->id,
                'voided_at' => now(),
                'updated_by' => $request->user()?->id,
            ]);

            // Cancel any pending payments
            $invoice->payments()->pending()->update([
                'status' => 'cancel',
            ]);

            AuditLogService::log($invoice, 'invoice.voided', [
                'old' => ['status' => $previousStatus],
                'new' => ['status' => 'void'],
                'description' => $request->void_reason,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Invoice voided successfully',
                'data' => $invoice->load('voidedBy')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to void invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function refund(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Only allow refund if status is paid
        if ($invoice->status !== 'paid') {
            return response()->json([
                'message' => 'Can only refund paid invoices'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'refund_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $lastSettledPayment = $invoice->payments()
                ->whereIn('status', ['settlement', 'capture'])
                ->latest('settlement_time')
                ->latest()
                ->first();

            if (!$lastSettledPayment) {
                DB::rollBack();
                return response()->json([
                    'message' => 'No settled payment found for this invoice'
                ], 422);
            }

            // Call Midtrans refund API
            $midtransService = new MidtransService();
            $refundResult = $midtransService->refundTransaction(
                $lastSettledPayment->order_id,
                null, // Full refund
                $request->refund_reason
            );

            if (!$refundResult['success']) {
                throw new \Exception('Midtrans refund failed: ' . $refundResult['error']);
            }

            $previousStatus = $invoice->status;
            $invoice->update([
                'status' => 'refund',
                'refund_reason' => $request->refund_reason,
                'refunded_by' => $request->user()?->id,
                'refunded_at' => now(),
                'updated_by' => $request->user()?->id,
            ]);

            AuditLogService::log($invoice, 'invoice.refunded', [
                'old' => ['status' => $previousStatus],
                'new' => ['status' => 'refund'],
                'description' => $request->refund_reason,
                'metadata' => [
                    'payment_id' => $lastSettledPayment->id,
                    'payment_order_id' => $lastSettledPayment->order_id,
                ],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Invoice refund initiated successfully',
                'data' => $invoice->load('refundedBy')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to refund invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Only allow delete if status is draft
        if ($invoice->status !== 'draft') {
            return response()->json([
                'message' => 'Can only delete draft invoices'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $invoice->delete();
            DB::commit();

            return response()->json([
                'message' => 'Invoice deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
