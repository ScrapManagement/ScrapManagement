<?php

namespace App\Services\Payment;

use App\Interfaces\PayableInterface;
use App\Interfaces\PaymentGatewayInterface;
use App\Models\Auction\Auction;
use App\Models\Auction\AuctionParticipant;
use App\Models\Payment\Payment;
use App\Services\Auctions\AuctionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InsurancePaymentService extends BasePaymentService implements PaymentGatewayInterface
{
    protected $api_key;
    protected $base_url;
    protected $integrations_id;
    protected $iframe_id;

    public function __construct()
    {
        $this->base_url        = config('services.paymob.base_url');
        $this->api_key         = config('services.paymob.api_key');
        $this->integrations_id = config('services.paymob.integration_id');
        $this->iframe_id       = config('services.paymob.iframe_id');

        $this->header = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    // ──────────────────────────────────────────
    // الـ user بيدفع التأمين → نفس flow الـ Paymob
    // ──────────────────────────────────────────

    public function sendPayment($user,  PayableInterface $payable, array $data = []): array
    {
        $amount = $payable->getAmount();
        $token        = $this->generateToken();
        $orderId      = $this->createOrder($token, $amount);
        $paymentToken = $this->generatePaymentKey($token, $orderId, $amount, $user);

        Payment::create([
            'user_id'    => $user->id,
            'package_id' => ($payable->getPaymentType() === 'package') ? $payable->getPayableId() : null,
            'auction_id' => ($payable->getPaymentType() === 'insurance') ? $payable->getPayableId() : null,
            'gateway'    => 'paymob',
            'type'       => $payable->getPaymentType(),
            'order_id'   => $orderId,
            'amount'     => $amount,
            'status'     => 'pending',
            'inspection_type' => $data['inspection_type'] ?? null,
        ]);

        $url = $this->buildIframeUrl($paymentToken);

        return [
            'success'    => true,
            'url'        => $url,
            'order_id'   => $orderId,

        ];
    }

    // ──────────────────────────────────────────
    // Paymob callback — نفس pattern الـ package
    // ──────────────────────────────────────────

    public function callBack(Request $request): bool
    {
        DB::beginTransaction();

        try {
            $data    = $request->all();
            Log::info('Insurance Paymob Callback', $data);

            $success = $data['success'] ?? false;
            $orderId = $data['order']   ?? null;
            $transactionId = $data['id'] ?? null;

            if (!$success || !$orderId) {
                Log::warning("Payment failed or Order ID missing for Order: $orderId");
                DB::rollBack();
                return false;
            }

            $payment = Payment::where('order_id', $orderId)
                ->where('type', 'insurance')
                ->where('status', 'pending')
                ->first();

            if (!$payment) {
                DB::rollBack();
                return false;
            }

            $user    = $payment->user;
            $auction = Auction::find($payment->auction_id);

            if (!$user || !$auction) {
                Log::error("User or Auction not found for Payment ID: {$payment->id}");
                DB::rollBack();
                return false;
            }

            $participant = AuctionParticipant::create([
                'auction_id'       => $auction->id,
                'user_id'          => $user->id,
                'insurance_amount' => $payment->amount,
                'insurance_status' => 'held',
                'inspection_type'  => $payment->inspection_type,
                'inspection_status' => 'pending',
            ]);

            // ربط الـ payment بالـ participant
            $payment->update([
                'status'                 => 'paid',
                'auction_participant_id' => $participant->id,
                'transaction_id' => $transactionId,
            ]);

            DB::commit();
            Log::info("Auction Participant created successfully: {$participant->id}");
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Insurance Callback Error: ' . $e->getMessage());
            return false;
        }
    }

    // ──────────────────────────────────────────
    // نفس methods الـ PaymobPaymentService
    // ──────────────────────────────────────────

    protected function generateToken(): string
    {
        $response = $this->buildRequest('POST', '/api/auth/tokens', [
            'api_key' => $this->api_key,
        ]);

        if (!$response['success']) {
            throw new \Exception('Paymob Auth Failed');
        }

        return $response['data']['token'];
    }

    protected function createOrder($token, $amount): int
    {
        $this->header['Authorization'] = 'Bearer ' . $token;

        $response = $this->buildRequest('POST', '/api/ecommerce/orders', [
            'amount_cents' => $amount * 100,
            'currency'     => 'EGP',
        ]);

        if (!$response['success']) {
            throw new \Exception('Create Order Failed');
        }

        return $response['data']['id'];
    }

    protected function generatePaymentKey($token, $orderId, $amount, $user): string
    {
        $this->header['Authorization'] = 'Bearer ' . $token;

        $response = $this->buildRequest('POST', '/api/acceptance/payment_keys', [
            'auth_token'     => $token,
            'amount_cents'   => $amount * 100,
            'expiration'     => 3600,
            'order_id'       => $orderId,
            'currency'       => 'EGP',
            'integration_id' => $this->integrations_id,
            'billing_data'   => [
                'first_name'   => $user->name     ?? 'User',
                'last_name'    => 'NA',
                'email'        => $user->email    ?? 'test@test.com',
                'phone_number' => $user->phone    ?? '01000000000',
                'city'         => 'Cairo',
                'country'      => 'EG',
                'street'       => 'NA',
                'building'     => 'NA',
                'floor'        => 'NA',
                'apartment'    => 'NA',
            ],
        ]);

        if (!$response['success']) {
            throw new \Exception('Payment Key Failed');
        }

        return $response['data']['token'];
    }


    public function refund(string $transactionId, float $amount): bool
    {
        try {
            $token = $this->generateToken();
            $this->header['Authorization'] = 'Bearer ' . $token;

            $payload = [
                'auth_token'   => $token,
                'transaction_id' => $transactionId,
                'amount_cents' => $amount * 100,
            ];

            $response = $this->buildRequest('POST', '/api/acceptance/void_refund/refund', $payload);

            if ($response['success'] && isset($response['data']['success']) && $response['data']['success'] === true) {
                Log::info("Paymob Refund successful for Order: $transactionId");
                return true;
            }

            Log::error("Paymob Refund failed for Order: $transactionId", $response);
            return false;

        } catch (\Exception $e) {
            Log::error("Refund Method Error: " . $e->getMessage());
            return false;
        }
    }

    protected function buildIframeUrl($paymentToken): string
    {
        return 'https://accept.paymob.com/api/acceptance/iframes/'
            . $this->iframe_id
            . '?payment_token='
            . $paymentToken;
    }
}
