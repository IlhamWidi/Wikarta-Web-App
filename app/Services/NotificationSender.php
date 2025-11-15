<?php

namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationSender
{
    public function sendEmail(?string $recipient, string $subject, string $message): void
    {
        if (empty($recipient)) {
            throw new \InvalidArgumentException('Email penerima tidak tersedia.');
        }

        Mail::raw($message, function ($mail) use ($recipient, $subject) {
            $mail->to($recipient)->subject($subject);
        });
    }

    public function sendWhatsapp(?string $recipient, string $message): bool
    {
        $apiUrl = config('services.whatsapp.api_url');
        $token = config('services.whatsapp.api_token');

        if (!$apiUrl || !$token) {
            Log::warning('WhatsApp API not configured');
            return false;
        }

        // Clean phone number (remove +, spaces, dashes)
        $number = preg_replace('/[^0-9]/', '', $recipient);
        
        // Ensure it starts with 62
        if (!str_starts_with($number, '62')) {
            if (str_starts_with($number, '0')) {
                $number = '62' . substr($number, 1);
            } else {
                $number = '62' . $number;
            }
        }

        if (!$number) {
            Log::warning('Invalid phone number for WhatsApp', ['recipient' => $recipient]);
            return false;
        }

        // Fonnte API format
        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->asForm()->post($apiUrl, [
            'target' => $number,
            'message' => $message,
            'countryCode' => '62',
        ]);

        $responseData = $response->json();
        
        // Check Fonnte-specific status field
        if ($response->failed() || (isset($responseData['status']) && $responseData['status'] === false)) {
            $error = $responseData['reason'] ?? $response->body();
            Log::warning('WhatsApp notification failed', [
                'number' => $number, 
                'error' => $error,
                'response' => $responseData
            ]);
            return false;
        }

        Log::info('WhatsApp notification sent successfully', [
            'number' => $number,
            'response' => $responseData
        ]);

        return true;
    }

    public function resendFromLog(NotificationLog $log): void
    {
        $message = $log->message ?? '';
        $subject = $log->subject ?? 'Notification';

        if ($log->channel === 'email') {
            $this->sendEmail($log->recipient, $subject, $message);
            return;
        }

        if ($log->channel === 'whatsapp') {
            $this->sendWhatsapp($log->recipient, $message);
            return;
        }

        throw new \InvalidArgumentException('Unsupported notification channel: ' . $log->channel);
    }
}
