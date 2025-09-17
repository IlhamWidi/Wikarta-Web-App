<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Exception;

class Midtrans extends Model
{
    use HasFactory;

    public static function bank_transfer($uri, $body)
    {
        try {
            $host = env('MIDTRANS_API_HOST', 'https://api.sandbox.midtrans.com/v2');
            $key = env('MIDTRANS_API_AUTH', 'U0ItTWlkLXNlcnZlci01LU1UTllVTzJqZ3pDd3Vhek1Rakd0bkE=');
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $key
            ])->post(sprintf("%s/%s", $host, $uri), $body)->json();
            return $response;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
}
