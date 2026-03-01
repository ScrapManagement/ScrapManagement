<?php

namespace App\Services\User;

use Illuminate\Support\Facades\Http;


class SmsService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected string $sender;

    public function __construct()
    {
        $this->baseUrl  = "https://otp.sell-io.app/send-otp";
    }

    public function sendOtp(string $phone, string $otp): bool
    {

        $response = Http::post($this->baseUrl . '/send-otp', [
            'phone'   => $phone,
            'message' => 'Your verification code is: ' . $otp,
            'otp'     => $otp,
        ]);
        if (!$response->ok()) {
            return false;
        }

        $data = $response->json();

        return isset($data['success']) && $data['success'] === true;
    }

    public function test(): array
    {
        try {
            $response = Http::post('https://otp.sell-io.app/send-otp', [
                'phone'   => '+201271491240', 
                'message' => 'Your verification code is: ',
                'otp'     => '123456',
            ]);

            return [
                'status'   => $response->status(),
                'success'  => $response->successful(),
                'response' => $response->json(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }
}
