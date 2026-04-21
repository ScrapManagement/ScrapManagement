<?php

namespace App\Services\Payment;

use App\Interfaces\PayableInterface;
use App\Interfaces\PaymentGatewayInterface;
use App\Models\Payment\Package;
use App\Models\Payment\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymobPaymentService extends BasePaymentService implements PaymentGatewayInterface
{
    /**
     * Create a new class instance.
     */
    protected $api_key;
    protected $base_url;
    protected $integrations_id;
    protected $iframe_id;

    public function __construct()
    {
        $this->base_url = config('services.paymob.base_url');
        $this->api_key  = config('services.paymob.api_key');

        $this->integrations_id = config('services.paymob.integration_id');
        $this->iframe_id      = config('services.paymob.iframe_id');

        $this->header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function sendPayment($user, PayableInterface $payable): array
    {
        $amount = $payable->getAmount();

        $token   = $this->generateToken();
        $orderId = $this->createOrder($token, $amount);
        $paymentToken = $this->generatePaymentKey($token, $orderId, $amount, $user);

        Payment::create([
            'user_id'      => $user->id,
            'package_id' => ($payable->getPaymentType() === 'package') ? $payable->getPayableId() : null,
            'auction_id' => ($payable->getPaymentType() === 'insurance') ? $payable->getPayableId() : null,
            'gateway'      => 'paymob',
            'type'       => $payable->getPaymentType(),
            'order_id'     => $orderId,
            'amount'       => $amount,
            'status'       => 'pending',
        ]);

        $url = $this->buildIframeUrl($paymentToken);

        return [
            'success'  => true,
            'url'      => $url,
            'order_id' => $orderId
        ];
    }

    //first generate token to access api
    protected function generateToken(): string
    {
        $response = $this->buildRequest('POST', '/api/auth/tokens', [
            'api_key' => $this->api_key
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
            "amount_cents" => $amount * 100,
            "currency"     => "EGP"
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
            "auth_token"     => $token,
            "amount_cents" => $amount * 100,
            "expiration"   => 3600,
            "order_id"     => $orderId,
            "currency"     => "EGP",
            "integration_id" => $this->integrations_id,
            "billing_data" => [
                "first_name" => $user->name ?? "User",
                "last_name"     => "NA",
                "email"      => $user->email ?? "test@test.com",
                "phone_number" => $user->phone ?? "01000000000",
                "city" => "Cairo",
                "country" => "EG",
                "street" => "NA",
                "building" => "NA",
                "floor" => "NA",
                "apartment" => "NA"
            ]
        ]);

        if (!$response['success']) {
            throw new \Exception('Payment Key Failed');
        }

        return $response['data']['token'];
    }

    public function callBack(Request $request): bool
    {
        DB::beginTransaction();

        try {

            $data = $request->all();

            Log::info('Paymob Callback', $data);

            $success = filter_var($data['success'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $orderId = $data['order'] ?? null;
            $transactionId = $data['id'] ?? null;

            if (!$orderId) {
                DB::rollBack();
                return false;
            }

            $payment = Payment::where('order_id', $orderId)
                ->where('type', 'package')
                ->where('status', 'pending')
                ->first();

            if (!$payment) {
                DB::rollBack();
                return false;
            }

            if (!$success) {
                $payment->update([
                    'status' => 'failed',
                    'transaction_id' => $transactionId,
                ]);
                DB::commit();
                return false;
            }

            $package = Package::find($payment->package_id);
            $user    = $payment->user;

            if (!$package || !$user) {
                DB::rollBack();
                return false;
            }

            app(CoinService::class)->purchasePackage($user, $package);

            $payment->update([
                'status' => 'paid',
                'transaction_id' => $transactionId,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Paymob Callback Error: ' . $e->getMessage());
            return false;
        }
    }

    protected function buildIframeUrl($paymentToken): string
    {
        return "https://accept.paymob.com/api/acceptance/iframes/"
            . $this->iframe_id
            . "?payment_token="
            . $paymentToken;
    }
}
