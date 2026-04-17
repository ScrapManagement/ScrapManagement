<?php


namespace App\Services\Notification;


use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = "https://otp.sell-io.app";
    }

    
    public function sendMessage(string $phone, string $message, string $otpValue = '0000'): bool
    {
        try {
            $response = Http::post($this->baseUrl . '/send-otp', [
                'phone'   => $phone,
                'message' => $message,
                'otp'     => $otpValue,
            ]);

            $data = $response->json() ?? [];

            info('WhatsApp Message Response', ['phone' => $phone, 'response' => $data]);

            return isset($data['success']) && $data['success'] === true;
        } catch (\Throwable $e) {
            info('WhatsApp Message Error', ['error' => $e->getMessage(), 'phone' => $phone]);
            return false;
        }
    }
}
