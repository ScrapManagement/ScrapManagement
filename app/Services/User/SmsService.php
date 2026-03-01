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
        $this->baseUrl  = "https://otp.sell-io.app";
    }

    public function sendOtp(string $phone, string $otp): bool
    {
        try {
            $response = Http::post($this->baseUrl . '/send-otp', [
                'phone'   => $phone,
                'message' => 'Your verification code is: ',
                'otp'     => $otp,
            ]);

            $data = $response->json() ?? [];

            info('OTP Response', $data);

            return isset($data['success']) && $data['success'] === true;
        } catch (\Throwable $e) {
            info('OTP Error', ['error' => $e->getMessage(), 'phone' => $phone, 'otp' => $otp]);
            return false;
        }
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
