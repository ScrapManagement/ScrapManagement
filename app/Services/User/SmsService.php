<?php

namespace App\Services\User;


class SmsService
{
    protected $client;

    public function __construct()
    {
        $this->client = new \Vonage\Client(
            new \Vonage\Client\Credentials\Basic(
                config('services.vonage.key'),
                config('services.vonage.secret')
            )
        );
    }

    public function sendOtp(string $phone, string $otp): bool
    {
        $message = "كود التحقق: $otp\nصالح لمدة دقيقتين";

        $response = $this->client->sms()->send(
            new \Vonage\SMS\Message\SMS(
                $phone,
                config('services.vonage.from'),
                $message
            )
        );

        return $response->current()->getStatus() === 0;
    }

    public function test(): array
    {
        try {
            $response = $this->client->sms()->send(
                new \Vonage\SMS\Message\SMS(
                    '+201011581323',
                    config('services.vonage.from'),
                    'Hello from Vonage SMS API!'
                )
            );

            $sms = $response->current();

            return [
                'success'    => $sms->getStatus() === 0,
                'status'     => $sms->getStatus(),
                'message_id' => $sms->getMessageId(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }
}
