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
        $this->baseUrl  = config('services.smsmisr.base_url');
        $this->username = config('services.smsmisr.username');
        $this->password = config('services.smsmisr.password');
        $this->sender   = config('services.smsmisr.sender');
    }

    public function sendOtp(string $phone, string $otp): bool
    {
        $message = "كود التحقق: $otp صالح لمدة دقيقتين";

        $response = Http::post($this->baseUrl . '/SMS/', [
            'username' => $this->username,
            'password' => $this->password,
            'language' => 2, // 1=English , 2=Arabic
            'sender'   => $this->sender,
            'mobile'   => $phone,
            'message'  => $message,
        ]);

        if (!$response->ok()) {
            return false;
        }

        $data = $response->json();

        return isset($data['code']) && $data['code'] == "1901";
    }

    public function test(): array
    {
        try {
            $response = Http::post($this->baseUrl . '/OTP/', [
                'username' => $this->username,
                'password' => $this->password,
                'language' => 1,
                'sender'   => $this->sender,
                'mobile'   => '201271491240',
                'message'  => 'Test SMS From Laravel',
            ]);

            return [
                'success' => $response->ok(),
                'response' => $response->json(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
