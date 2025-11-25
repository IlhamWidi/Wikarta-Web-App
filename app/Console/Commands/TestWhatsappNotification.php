<?php

namespace App\Console\Commands;

use App\Services\NotificationSender;
use Illuminate\Console\Command;

class TestWhatsappNotification extends Command
{
    protected $signature = 'whatsapp:test {phone} {message?}';

    protected $description = 'Test WhatsApp notification';

    public function handle(NotificationSender $sender): int
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message') ?? 'Test pesan dari Wikarta! 🚀';

        // Anti-spam protection: Confirm before sending
        if (!$this->confirm("⚠️  Ini akan mengirim WhatsApp ke {$phone}. Lanjutkan?", true)) {
            $this->warn('❌ Test dibatalkan');
            return self::FAILURE;
        }

        $this->info("Mengirim WhatsApp ke: {$phone}");
        $this->info("Pesan: {$message}");

        try {
            $result = $sender->sendWhatsapp($phone, $message);

            if ($result) {
                $this->info('✅ WhatsApp berhasil dikirim!');
                return self::SUCCESS;
            } else {
                $this->error('❌ WhatsApp gagal dikirim. Cek log untuk detail.');
                return self::FAILURE;
            }
        } catch (\Throwable $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
