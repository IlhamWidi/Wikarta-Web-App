<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected $host;
    protected $token;
    protected $groupId;

    public function __construct()
    {
        $this->host = config('services.go_wa.host', env('GO_WA_HOST'));
        $this->token = config('services.go_wa.token', env('GO_WA_TOKEN'));
        $this->groupId = config('services.go_wa.group_id', env('GO_WA_GROUP_ID'));
    }

    /**
     * Send WhatsApp text message to group
     *
     * @param string $message
     * @param string|null $msisdn
     * @return array
     */
    public function sendText($message, $msisdn = null)
    {
        $msisdn = $msisdn ?: $this->groupId;
        $url = rtrim($this->host, '/') . '/api/v1/whatsapp/send/text';
        $response = Http::asMultipart()
            ->withHeaders([
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])
            ->post($url, [
                ['name' => 'msisdn', 'contents' => $msisdn],
                ['name' => 'message', 'contents' => $message],
            ]);
        if ($response->successful()) {
            return $response->json();
        }
        return [
            'error' => true,
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }
}
