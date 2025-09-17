<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Notification extends Model
{
    use HasFactory;

    public static function SendMessage($phoneNumber, $message)
    {
        try {
            $gatewayUrl = env('WA_GATEWAY_URL', 'http://gateway.wikarta.co.id/send');
            $sessionId = env('WA_SESSION_ID', '6285176782089');
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($gatewayUrl, [
                        'sessionId' => $sessionId,
                        'phoneNumber' => Helper::FormatPhoneNumber($phoneNumber),
                        'message' => $message,
                    ]);
            return $response->body();
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function GoSendMessage($phoneNumber, $message)
    {
        $phoneNumber = Helper::FormatPhoneNumber($phoneNumber);

        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => env('GO_WA_HOST') . '/api/v1/whatsapp/send/text',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('msisdn' => $phoneNumber, 'message' => $message),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('GO_WA_TOKEN'),
                    'Content-Type: multipart/form-data'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
}

