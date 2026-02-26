<?php

namespace App\Interfaces;

use App\Models\Payment\Package;
use App\Models\User\User;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function sendPayment(User $user, Package $package): array;

    public function callBack(Request $request);
}
