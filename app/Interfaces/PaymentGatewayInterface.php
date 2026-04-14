<?php

namespace App\Interfaces;

use App\Interfaces\PayableInterface;
use App\Models\User\User;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function sendPayment(User $user, PayableInterface $payable): array;

    public function callBack(Request $request);
}
