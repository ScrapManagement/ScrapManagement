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
          return redirect()->away("https://scrapfe.sell-io.app/payment-failed");
        }

        $service = match($payment->type) {
            'package'   => app(PaymobPaymentService::class),
            'insurance' => app(InsurancePaymentService::class),
            default     => null,
        };

        if (!$service) {
             return redirect()->away("https://scrapfe.sell-io.app/payment-failed");
        }

        $response = $service->callBack($request);

        if ($response) {
             return redirect()->away("https://scrapfe.sell-io.app/payment-success");
        } else {
             return redirect()->away("https://scrapfe.sell-io.app/payment-failed");
        }

       /*  return $response
            ? redirect()->route('payment.success')
            : redirect()->route('payment.failed'); */
    }

   
}
