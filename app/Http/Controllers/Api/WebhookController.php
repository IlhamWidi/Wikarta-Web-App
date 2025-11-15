<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebhookEvent;
use App\Models\Payment;
use App\Models\Invoice;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_webhooks')->only(['index', 'show']);
        $this->middleware('permission:manage_webhooks')->only('retry');
    }

    /**
     * Handle Midtrans webhook notification
     */
    public function midtrans(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans webhook received', ['payload' => $payload]);

        $midtransService = new MidtransService();

        // Verify signature
        if (!$midtransService->verifySignature($payload)) {
            Log::warning('Invalid Midtrans webhook signature');
            return response()->json([
                'message' => 'Invalid signature'
            ], 401);
        }

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (!$orderId || !$transactionStatus) {
            Log::error('Webhook payload missing required identifiers', ['payload' => $payload]);
            return response()->json(['message' => 'Invalid payload'], 422);
        }

        // Create event key for idempotency
        $eventKey = $orderId . '_' . $transactionStatus . '_' . ($payload['transaction_time'] ?? time());

        // Check if event already processed
        if (WebhookEvent::where('event_key', $eventKey)->exists()) {
            Log::info('Webhook already processed', ['event_key' => $eventKey]);
            return response()->json(['message' => 'Event already processed'], 200);
        }

        DB::beginTransaction();
        try {
            // Store webhook event
            $webhookEvent = WebhookEvent::create([
                'event_type' => 'midtrans.notification',
                'event_key' => $eventKey,
                'order_id' => $orderId,
                'transaction_id' => $payload['transaction_id'] ?? null,
                'transaction_status' => $transactionStatus,
                'payment_type' => $payload['payment_type'] ?? null,
                'fraud_status' => $fraudStatus,
                'gross_amount' => (float) ($payload['gross_amount'] ?? 0),
                'transaction_time' => $payload['transaction_time'] ?? null,
                'settlement_time' => $payload['settlement_time'] ?? null,
                'payload' => $payload,
                'signature_key' => $payload['signature_key'] ?? null,
                'is_verified' => true,
                'is_processed' => false,
            ]);

            // Find payment by order_id
            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                Log::error('Payment not found for webhook', ['order_id' => $orderId]);
                $webhookEvent->update([
                    'processing_error' => 'Payment not found',
                    'retry_count' => 1,
                ]);
                DB::commit();
                return response()->json(['message' => 'Payment not found'], 404);
            }

            // Update payment based on transaction status
            $paymentStatus = $this->mapMidtransStatus($transactionStatus, $fraudStatus);
            
            $payment->update([
                'transaction_id' => $payload['transaction_id'] ?? null,
                'payment_type' => $payload['payment_type'] ?? $payment->payment_type,
                'status' => $paymentStatus,
                'settlement_time' => ($paymentStatus === 'settlement') ? now() : $payment->settlement_time,
                'metadata' => array_merge($payment->metadata ?? [], [
                    'midtrans_status' => $transactionStatus,
                    'fraud_status' => $fraudStatus,
                    'updated_at' => now()->toDateTimeString(),
                ]),
            ]);

            // Update invoice status
            $invoice = $payment->invoice;
            if ($invoice) {
                if (in_array($paymentStatus, ['settlement', 'capture'])) {
                    $invoice->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
                } elseif (in_array($paymentStatus, ['deny', 'cancel', 'expire'])) {
                    // Check if there are other pending payments
                    $hasPendingPayments = $invoice->payments()
                        ->where('id', '!=', $payment->id)
                        ->whereIn('status', ['pending', 'challenge'])
                        ->exists();
                    
                    if (!$hasPendingPayments) {
                        $invoice->update(['status' => 'issued']);
                    }
                }
            }

            // Mark webhook as processed
            $webhookEvent->update([
                'is_processed' => true,
                'processed_at' => now(),
            ]);

            DB::commit();

            Log::info('Webhook processed successfully', [
                'order_id' => $orderId,
                'payment_status' => $paymentStatus
            ]);

            return response()->json([
                'message' => 'Webhook processed successfully'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);

            // Update webhook event with error
            if (isset($webhookEvent)) {
                $webhookEvent->update([
                    'processing_error' => $e->getMessage(),
                    'retry_count' => ($webhookEvent->retry_count ?? 0) + 1,
                ]);
            }

            return response()->json([
                'message' => 'Webhook processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map Midtrans transaction status to our payment status
     */
    private function mapMidtransStatus($transactionStatus, $fraudStatus): string
    {
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                return 'capture';
            } elseif ($fraudStatus === 'challenge') {
                return 'challenge';
            } else {
                return 'deny';
            }
        } elseif ($transactionStatus === 'settlement') {
            return 'settlement';
        } elseif ($transactionStatus === 'pending') {
            return 'pending';
        } elseif ($transactionStatus === 'deny') {
            return 'deny';
        } elseif ($transactionStatus === 'cancel' || $transactionStatus === 'expire') {
            return 'cancel';
        } elseif ($transactionStatus === 'refund') {
            return 'refund';
        }

        return 'pending';
    }

    /**
     * Get webhook event details
     */
    public function show($id)
    {
        $event = WebhookEvent::findOrFail($id);

        return response()->json([
            'data' => $event
        ]);
    }

    /**
     * List webhook events with filtering
     */
    public function index(Request $request)
    {
        $query = WebhookEvent::query();

        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->has('is_processed')) {
            $query->where('is_processed', $request->boolean('is_processed'));
        }

        if ($request->has('has_error')) {
            if ($request->boolean('has_error')) {
                $query->whereNotNull('processing_error');
            } else {
                $query->whereNull('processing_error');
            }
        }

        $allowedSortColumns = ['id', 'event_type', 'order_id', 'transaction_status', 'created_at', 'processed_at'];
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, $allowedSortColumns) && in_array($sortOrder, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->get('per_page', 15);
        $events = $query->paginate($perPage);

        return response()->json($events);
    }

    /**
     * Retry failed webhook processing
     */
    public function retry($id)
    {
        $event = WebhookEvent::findOrFail($id);

        if ($event->is_processed) {
            return response()->json([
                'message' => 'Event already processed'
            ], 422);
        }

        if ($event->retry_count >= 5) {
            return response()->json([
                'message' => 'Maximum retry attempts reached'
            ], 422);
        }

        // Recreate request from payload
        $request = new Request($event->payload);

        // Process webhook again
        return $this->midtrans($request);
    }
}
