<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Invoice;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Midtrans\CoreApi;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create Snap transaction token for payment
     */
    public function createSnapToken(Payment $payment): array
    {
        try {
            $invoice = $payment->invoice;
            $customer = $invoice->customer;

            $transactionDetails = [
                'order_id' => $payment->order_id,
                'gross_amount' => (int) $payment->amount,
            ];

            $itemDetails = [];
            foreach ($invoice->items as $item) {
                $itemDetails[] = [
                    'id' => $item->id,
                    'price' => (int) $item->price,
                    'quantity' => (int) $item->quantity,
                    'name' => $item->description,
                ];
            }

            // Add tax if any
            if ($invoice->tax > 0) {
                $itemDetails[] = [
                    'id' => 'TAX',
                    'price' => (int) $invoice->tax,
                    'quantity' => 1,
                    'name' => 'Tax',
                ];
            }

            // Add discount if any
            if ($invoice->discount > 0) {
                $itemDetails[] = [
                    'id' => 'DISCOUNT',
                    'price' => -(int) $invoice->discount,
                    'quantity' => 1,
                    'name' => 'Discount',
                ];
            }

            $customerDetails = [
                'first_name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'billing_address' => [
                    'address' => $customer->address,
                    'city' => 'Jakarta', // Could be extracted from customer data
                    'postal_code' => '12345',
                    'country_code' => 'IDN',
                ],
            ];

            $params = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'enabled_payments' => $this->getEnabledPayments($payment->payment_method),
                'expiry' => [
                    'start_time' => date('Y-m-d H:i:s O'),
                    'unit' => 'hours',
                    'duration' => 24, // 24 hours expiry
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            $redirectBase = rtrim(config('services.midtrans.snap_redirect_url'), '/');
            $redirectUrl = $redirectBase . '/' . $snapToken;

            // Update payment with snap token
            $payment->update([
                'metadata' => array_merge($payment->metadata ?? [], [
                    'snap_token' => $snapToken,
                    'created_at' => now()->toDateTimeString(),
                ]),
                'payment_url' => $redirectUrl,
                'expiry_time' => now()->addHours(24),
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => $redirectUrl,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to create Snap token', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create Virtual Account transaction
     */
    public function createVirtualAccount(Payment $payment, string $bank = 'bca'): array
    {
        try {
            $invoice = $payment->invoice;
            $customer = $invoice->customer;

            $params = [
                'payment_type' => 'bank_transfer',
                'transaction_details' => [
                    'order_id' => $payment->order_id,
                    'gross_amount' => (int) $payment->amount,
                ],
                'bank_transfer' => [
                    'bank' => $bank, // bca, bni, bri, permata, etc.
                ],
                'customer_details' => [
                    'first_name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
            ];

            $response = CoreApi::charge($params);

            if ($response->status_code === '201') {
                // Extract VA number
                $vaNumber = null;
                if (isset($response->va_numbers[0])) {
                    $vaNumber = $response->va_numbers[0]->va_number;
                } elseif (isset($response->permata_va_number)) {
                    $vaNumber = $response->permata_va_number;
                }

                // Update payment
                $payment->update([
                    'transaction_id' => $response->transaction_id,
                    'va_number' => $vaNumber,
                    'payment_type' => $bank . '_va',
                    'status' => 'pending',
                    'expiry_time' => now()->addHours(24),
                    'metadata' => array_merge($payment->metadata ?? [], [
                        'bank' => $bank,
                        'response' => (array) $response,
                    ]),
                ]);

                return [
                    'success' => true,
                    'va_number' => $vaNumber,
                    'bank' => $bank,
                    'transaction_id' => $response->transaction_id,
                ];
            }

            return [
                'success' => false,
                'error' => $response->status_message ?? 'Failed to create VA',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to create Virtual Account', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create QRIS transaction
     */
    public function createQRIS(Payment $payment): array
    {
        try {
            $invoice = $payment->invoice;
            $customer = $invoice->customer;

            $params = [
                'payment_type' => 'gopay',
                'transaction_details' => [
                    'order_id' => $payment->order_id,
                    'gross_amount' => (int) $payment->amount,
                ],
                'customer_details' => [
                    'first_name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
            ];

            $response = CoreApi::charge($params);

            if ($response->status_code === '201') {
                $qrisUrl = $response->actions[0]->url ?? null;

                $payment->update([
                    'transaction_id' => $response->transaction_id,
                    'qr_code_url' => $qrisUrl,
                    'payment_type' => 'qris',
                    'status' => 'pending',
                    'expiry_time' => now()->addMinutes(30),
                    'metadata' => array_merge($payment->metadata ?? [], [
                        'response' => (array) $response,
                    ]),
                ]);

                return [
                    'success' => true,
                    'qris_url' => $qrisUrl,
                    'transaction_id' => $response->transaction_id,
                ];
            }

            return [
                'success' => false,
                'error' => $response->status_message ?? 'Failed to create QRIS',
            ];

        } catch (\Exception $e) {
            Log::error('Failed to create QRIS', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getTransactionStatus($orderId): array
    {
        try {
            $status = Transaction::status($orderId);

            return [
                'success' => true,
                'data' => (array) $status,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get transaction status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel/void transaction
     */
    public function cancelTransaction($orderId): array
    {
        try {
            $response = Transaction::cancel($orderId);

            return [
                'success' => true,
                'data' => (array) $response,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to cancel transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refund transaction
     */
    public function refundTransaction($orderId, $amount = null, $reason = null): array
    {
        try {
            $params = [];
            
            if ($amount) {
                $params['amount'] = (int) $amount;
            }
            
            if ($reason) {
                $params['reason'] = $reason;
            }

            $response = Transaction::refund($orderId, $params);

            return [
                'success' => true,
                'data' => (array) $response,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to refund transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get enabled payment methods based on preference
     */
    private function getEnabledPayments($preferredMethod = null): array
    {
        $allMethods = [
            'credit_card',
            'bca_va',
            'bni_va',
            'bri_va',
            'permata_va',
            'other_va',
            'gopay',
            'shopeepay',
            'qris',
        ];

        // If specific method preferred, prioritize it
        if ($preferredMethod) {
            return array_unique(array_merge([$preferredMethod], $allMethods));
        }

        return $allMethods;
    }

    /**
     * Verify webhook signature
     */
    public function verifySignature(array $data): bool
    {
        $serverKey = config('services.midtrans.server_key');
        $orderId = $data['order_id'] ?? '';
        $statusCode = $data['status_code'] ?? '';
        $grossAmount = $data['gross_amount'] ?? '';
        $signatureKey = $data['signature_key'] ?? '';

        $hash = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return $hash === $signatureKey;
    }
}
