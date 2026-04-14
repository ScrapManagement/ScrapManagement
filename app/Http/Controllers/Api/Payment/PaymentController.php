<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Interfaces\PaymentGatewayInterface;
use App\Models\Payment\Payment;
use App\Services\Payment\InsurancePaymentService;
use App\Services\Payment\PaymobPaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /* protected PaymentGatewayInterface $paymentGateway;

    public function __construct(PaymentGatewayInterface $paymentGateway)
    {

        $this->paymentGateway = $paymentGateway;
    } */




   public function callBack(Request $request): \Illuminate\Http\RedirectResponse
    {
        $orderId = $request->get('order');

        $payment = Payment::where('order_id', $orderId)
            ->where('status', 'pending')
            ->first();

        if (!$payment) {
            return redirect()->route('payment.failed');
        }

        $service = match($payment->type) {
            'package'   => app(PaymobPaymentService::class),
            'insurance' => app(InsurancePaymentService::class),
            default     => null,
        };

        if (!$service) {
            return redirect()->route('payment.failed');
        }

        $response = $service->callBack($request);

        return $response
            ? redirect()->route('payment.success')
            : redirect()->route('payment.failed');
    }

    public function success()
    {
        return view('payment-success');
    }

    public function failed()
    {
        return view('payment-failed');
    }
}
