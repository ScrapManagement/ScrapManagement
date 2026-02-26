<?php

namespace App\Services\Payment;

use Exception;
use Illuminate\Support\Facades\Http;

class BasePaymentService
{
    /**
     * Create a new class instance.
     */
    protected  $base_url;
    protected array $header;
    protected function buildRequest($method, $url, $data = null, $type = 'json'): array
    {
        try {
            $response = Http::withHeaders($this->header)
                ->send($method, $this->base_url . $url, [
                    $type => $data
                ]);

            return [
                'success' => $response->successful(),
                'status'  => $response->status(),
                'data'    => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status'  => 500,
                'message' => $e->getMessage(),
            ];
        }
    }
}
