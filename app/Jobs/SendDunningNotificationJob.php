<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\NotificationLog;
use App\Services\NotificationSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDunningNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Invoice $invoice;
    public string $stage;

    public function __construct(Invoice $invoice, string $stage)
    {
        $this->invoice = $invoice;
        $this->stage = $stage;
    }

    public function handle(NotificationSender $notificationSender): void
    {
        $customer = $this->invoice->customer;

        if (!$customer) {
            Log::warning('Skipping dunning notification because customer is missing', [
                'invoice_id' => $this->invoice->id,
            ]);
            return;
        }

        // Format tanggal dalam bahasa Indonesia
        $months = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        
        $dateEnglish = $this->invoice->due_date ? $this->invoice->due_date->format('d F Y') : '-';
        $dueDate = str_replace(array_keys($months), array_values($months), $dateEnglish);
        $formattedTotal = number_format($this->invoice->total, 0, ',', '.');

        $subject = "Pengingat Pembayaran Tagihan {$this->invoice->invoice_number} 🕒";
        $message = "Halo {$customer->name},\n\n"
            . "Kami ingin mengingatkan bahwa tagihan {$this->invoice->invoice_number} sebesar Rp {$formattedTotal} akan jatuh tempo pada {$dueDate}.\n"
            . "Mohon segera menyelesaikan pembayaran agar layanan Anda tetap aktif tanpa gangguan.\n\n"
            . "Jika Anda sudah melakukan pembayaran, abaikan pesan ini.\n"
            . "Terima kasih atas kerja sama dan kepercayaannya.\n\n"
            . "Salam,\n"
            . "Tim Wikarta Provider";

        $log = NotificationLog::create([
            'notification_id' => $this->invoice->id,
            'type' => 'invoice.dunning',
            'notifiable_type' => Invoice::class,
            'notifiable_id' => $this->invoice->id,
            'channel' => 'email',
            'recipient' => $customer->email,
            'subject' => $subject,
            'message' => $message,
            'metadata' => [
                'stage' => $this->stage,
                'customer' => $customer->only(['name', 'email', 'phone']),
                'total' => $this->invoice->total,
                'due_date' => $dueDate,
            ],
            'status' => 'pending',
        ]);

        try {
            $notificationSender->sendEmail($customer->email, $subject, $message);

            try {
                $notificationSender->sendWhatsapp($customer->phone, strip_tags($message));
            } catch (\Throwable $exception) {
                Log::warning('WhatsApp dunning notification failed', [
                    'invoice_id' => $this->invoice->id,
                    'error' => $exception->getMessage(),
                ]);
            }

            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
                'failed_at' => null,
                'error_message' => null,
            ]);
        } catch (\Throwable $e) {
            $log->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
